<?php

use App\Models\Receipt;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('receipts', function (Blueprint $table) {
            $table->string('receipt_number')->nullable()->unique()->after('id');
        });

        $perYear = [];
        Receipt::withTrashed()->orderBy('issued_at')->chunk(200, function ($receipts) use (&$perYear) {
            foreach ($receipts as $receipt) {
                $year = optional($receipt->issued_at)->format('Y') ?? date('Y');
                $perYear[$year] = ($perYear[$year] ?? 0) + 1;
                $receipt->receipt_number = sprintf('RCV-%s-%06d', $year, $perYear[$year]);
                $receipt->saveQuietly();
            }
        });
    }

    public function down(): void
    {
        Schema::table('receipts', function (Blueprint $table) {
            $table->dropUnique(['receipt_number']);
            $table->dropColumn('receipt_number');
        });
    }
};
