<?php

namespace App\Console\Commands;

use App\Models\BuiltPropertyListingLead;
use App\Models\LandPurchaseLead;
use App\Models\LandSaleLead;
use App\Models\LeadActivity;
use App\Models\RentalConsultation;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ImportClientRequests extends Command
{
    protected $signature = 'revolest:import-client-requests
        {path : Path to the CSV file (e.g. docs/xls/client-request-25.csv)}
        {--dry-run : Parse and report without writing to DB}';

    protected $description = 'Import legacy client request rows from the Excel "Client Request-25" sheet (CSV export) into the appropriate lead models, with the Notes column captured as lead_activities.';

    public function handle(): int
    {
        $path = $this->argument('path');
        if (! is_file($path)) {
            $this->error("File not found: {$path}");
            return self::FAILURE;
        }

        $dryRun = (bool) $this->option('dry-run');

        $rows = $this->readCsv($path);
        if (empty($rows)) {
            $this->error('CSV had no data rows.');
            return self::FAILURE;
        }

        $stats = ['imported' => 0, 'skipped' => 0, 'activities' => 0, 'by_type' => []];

        foreach ($rows as $i => $row) {
            $row = array_map(fn ($v) => is_string($v) ? trim($v) : $v, $row);
            $clientName = $row['client'] ?? null;
            if (blank($clientName)) {
                continue;
            }

            $bucket = $this->categorize($row['request_type'] ?? '', $row['property_request'] ?? '');
            $stats['by_type'][$bucket] = ($stats['by_type'][$bucket] ?? 0) + 1;

            if ($bucket === 'skip') {
                $stats['skipped']++;
                $this->warn("  Row {$i}: skipping ({$row['request_type']}) — {$clientName}");
                continue;
            }

            if ($dryRun) {
                $stats['imported']++;
                $this->line("  Row {$i}: would import as {$bucket} — {$clientName}");
                continue;
            }

            [$lead, $created] = $this->upsertLead($bucket, $row);
            if (! $created) {
                $this->warn("  Row {$i}: existing lead matched — {$clientName}; will append activities only");
            } else {
                $stats['imported']++;
            }

            $activityCount = $this->appendActivities($lead, $row);
            $stats['activities'] += $activityCount;
        }

        $this->newLine();
        $this->info('Done.');
        $this->table(
            ['Metric', 'Value'],
            [
                ['Imported', $stats['imported']],
                ['Skipped', $stats['skipped']],
                ['Activities created', $stats['activities']],
            ]
        );
        foreach ($stats['by_type'] as $bucket => $count) {
            $this->line("  → {$bucket}: {$count}");
        }

        return self::SUCCESS;
    }

    private function readCsv(string $path): array
    {
        $fh = fopen($path, 'r');
        $header = fgetcsv($fh);
        $map = $this->buildHeaderMap($header);

        $rows = [];
        while (($row = fgetcsv($fh)) !== false) {
            $assoc = [];
            foreach ($map as $key => $idx) {
                $assoc[$key] = $row[$idx] ?? null;
            }
            $rows[] = $assoc;
        }
        fclose($fh);

        return $rows;
    }

    private function buildHeaderMap(array $header): array
    {
        $needles = [
            'nu' => 'nu',
            'client' => 'client',
            'date' => 'date',
            'property_request' => 'property request',
            'request_type' => 'request type',
            'plot_size' => 'plot size',
            'description' => 'property description',
            'location' => 'prefered location',
            'budget' => 'budget',
            'contact' => 'contact',
            'email' => 'email',
            'notes' => 'notes',
            'referrals' => 'referrals',
        ];

        $map = [];
        foreach ($header as $idx => $title) {
            $normalized = strtolower(trim(preg_replace('/\s+/', ' ', (string) $title)));
            foreach ($needles as $key => $needle) {
                if (str_contains($normalized, $needle) && ! isset($map[$key])) {
                    $map[$key] = $idx;
                }
            }
        }

        return $map;
    }

    private function categorize(string $requestType, string $propertyRequest): string
    {
        $haystack = strtolower($requestType . ' ' . $propertyRequest);

        if (str_contains($haystack, 'investment')) {
            return 'skip';
        }
        if (str_contains($haystack, 'management')) {
            return 'skip';
        }
        if (str_contains($haystack, 'buy') || str_contains($haystack, 'looking for buy')) {
            return 'land_purchase';
        }
        if (str_contains($haystack, 'rent')) {
            return 'rental';
        }
        if (str_contains($haystack, 'built') || str_contains($haystack, 'house')) {
            return 'built_listing';
        }
        if (str_contains($haystack, 'sell') || str_contains($haystack, 'sale') || str_contains($haystack, 'compound')) {
            return 'land_sale';
        }

        return 'skip';
    }

    private function upsertLead(string $bucket, array $row): array
    {
        $name = $row['client'] ?? '';
        $phone = $row['contact'] ?? '';
        $email = $row['email'] ?? null;
        $referredBy = $row['referrals'] ?? null;
        $location = $row['location'] ?? null;
        $plotSize = $row['plot_size'] ?? null;
        $budgetRaw = $row['budget'] ?? null;
        [$budgetMin, $budgetMax] = $this->parseBudget($budgetRaw);
        $submittedAt = $this->parseDate($row['date'] ?? null);

        $shared = [
            'phone' => $phone ?: '0000000',
            'email' => $email ?: null,
            'budget' => $budgetRaw ?: null,
            'budget_min' => $budgetMin,
            'budget_max' => $budgetMax,
            'referred_by_name' => $referredBy ?: null,
            'submitted_at' => $submittedAt,
            'status' => 'new',
        ];

        return match ($bucket) {
            'land_purchase' => $this->makeOrMatch(
                LandPurchaseLead::class,
                ['full_name' => $name, 'phone' => $phone],
                array_merge($shared, [
                    'full_name' => $name,
                    'preferred_locations' => $location,
                    'plot_size' => $plotSize,
                ])
            ),
            'land_sale' => $this->makeOrMatch(
                LandSaleLead::class,
                ['full_name' => $name, 'phone_primary' => $phone],
                array_merge($shared, [
                    'full_name' => $name,
                    'phone_primary' => $phone,
                    'land_location' => $location,
                    'land_size' => $plotSize,
                ])
            ),
            'rental' => $this->makeOrMatch(
                RentalConsultation::class,
                ['full_name' => $name, 'phone' => $phone],
                array_merge($shared, [
                    'full_name' => $name,
                    'preferred_locations' => $location,
                ])
            ),
            'built_listing' => (function () use ($shared, $name, $phone, $location) {
                $parts = preg_split('/\s+/', trim($name), 2);
                $first = $parts[0] ?? $name;
                $last = $parts[1] ?? '-';

                return $this->makeOrMatch(
                    BuiltPropertyListingLead::class,
                    ['first_name' => $first, 'phone' => $phone],
                    array_merge($shared, [
                        'first_name' => $first,
                        'last_name' => $last,
                        'property_address' => $location ?: 'Unknown (imported from legacy spreadsheet)',
                    ])
                );
            })(),
        };
    }

    private function makeOrMatch(string $modelClass, array $matchAttrs, array $payload): array
    {
        $existing = $modelClass::query();
        foreach ($matchAttrs as $field => $value) {
            $existing->where($field, $value);
        }
        $existing = $existing->first();

        if ($existing) {
            return [$existing, false];
        }

        $payload = array_filter($payload, fn ($v) => $v !== null && $v !== '');
        $lead = $modelClass::create($payload);

        return [$lead, true];
    }

    private function parseBudget(?string $raw): array
    {
        if (blank($raw)) {
            return [null, null];
        }

        preg_match_all('/[\d,\.]+/', $raw, $matches);
        $nums = array_filter(array_map(
            fn ($s) => (float) str_replace([',', ' '], '', $s),
            $matches[0] ?? []
        ));

        if (empty($nums)) {
            return [null, null];
        }

        $min = min($nums);
        $max = max($nums);

        return [$min, $max > $min ? $max : null];
    }

    private function parseDate(?string $raw): ?Carbon
    {
        if (blank($raw)) {
            return null;
        }

        foreach (['Y-m-d H:i:s', 'Y-m-d', 'd/m/y', 'd/m/Y'] as $format) {
            try {
                return Carbon::createFromFormat($format, $raw);
            } catch (\Throwable) {
                continue;
            }
        }

        try {
            return Carbon::parse($raw);
        } catch (\Throwable) {
            return null;
        }
    }

    private function appendActivities($lead, array $row): int
    {
        $created = 0;

        $notes = $row['notes'] ?? null;
        if (filled($notes)) {
            $lead->activities()->create([
                'kind' => 'note',
                'body' => $notes,
                'metadata' => ['source' => 'excel-import', 'sheet' => 'Client Request-25'],
            ]);
            $created++;
        }

        $description = $row['description'] ?? null;
        if (filled($description) && $description !== $notes) {
            $lead->activities()->create([
                'kind' => 'note',
                'body' => 'Property description: ' . $description,
                'metadata' => ['source' => 'excel-import'],
            ]);
            $created++;
        }

        return $created;
    }
}
