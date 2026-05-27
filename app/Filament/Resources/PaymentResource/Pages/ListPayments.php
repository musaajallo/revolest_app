<?php

namespace App\Filament\Resources\PaymentResource\Pages;

use App\Filament\Resources\PaymentResource;
use App\Models\Payment;
use App\Support\CsvDownload;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPayments extends ListRecords
{
    protected static string $resource = PaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            CsvDownload::action(
                'payments',
                [
                    'Nu',
                    'Tenant Name',
                    'Unit Occupied - Nu',
                    'Address',
                    'Duration',
                    'Amount Paid',
                    'Purpose',
                    'Payment date',
                    'Receipt Nu',
                    'Payment Method',
                    'Outstanding Balance',
                    'Paid by',
                    'Received by',
                    'CMS. EARN',
                    'Payment Status',
                    'Remarks',
                    'Property Owner',
                ],
                fn () => Payment::withTrashed()
                    ->with(['lease.property.owner', 'tenant', 'owner', 'receivedBy', 'receipt'])
                    ->orderBy('payment_date', 'desc')
                    ->cursor()
                    ->map(fn (Payment $p) => [
                        $p->id,
                        $p->tenant?->name,
                        $p->lease?->property?->title,
                        $p->lease?->property?->address,
                        $p->period_label ?: trim(
                            ($p->period_start?->format('Y-m-d') ?? '')
                            . ($p->period_end ? ' → ' . $p->period_end->format('Y-m-d') : '')
                        ),
                        $p->amount,
                        Payment::PURPOSES[$p->purpose] ?? $p->purpose,
                        $p->payment_date?->format('Y-m-d'),
                        $p->receipt?->receipt_number,
                        $p->method,
                        $p->outstandingBalance(),
                        $p->paid_by_name,
                        $p->receivedBy?->name,
                        $p->commission_amount,
                        Payment::STATUSES[$p->status] ?? $p->status,
                        $p->notes,
                        $p->owner?->name ?: $p->lease?->property?->owner?->name,
                    ])
            ),
        ];
    }
}
