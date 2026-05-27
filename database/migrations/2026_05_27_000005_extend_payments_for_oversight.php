<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->string('purpose')->default('rent')->after('amount');
            $table->date('period_start')->nullable()->after('purpose');
            $table->date('period_end')->nullable()->after('period_start');
            $table->string('period_label')->nullable()->after('period_end');

            $table->decimal('expected_amount', 15, 2)->nullable()->after('amount');
            $table->decimal('commission_amount', 15, 2)->nullable()->after('expected_amount');

            $table->string('paid_by_name')->nullable()->after('method');
            $table->foreignId('received_by_user_id')->nullable()->after('paid_by_name')->constrained('users')->nullOnDelete();

            $table->text('notes')->nullable()->after('receipt_file');

            $table->index('purpose');
            $table->index('payment_date');
            $table->index('period_start');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex(['purpose']);
            $table->dropIndex(['payment_date']);
            $table->dropIndex(['period_start']);

            $table->dropForeign(['received_by_user_id']);

            $table->dropColumn([
                'purpose',
                'period_start',
                'period_end',
                'period_label',
                'expected_amount',
                'commission_amount',
                'paid_by_name',
                'received_by_user_id',
                'notes',
            ]);
        });
    }
};
