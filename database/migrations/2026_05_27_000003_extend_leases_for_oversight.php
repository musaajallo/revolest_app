<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leases', function (Blueprint $table) {
            $table->decimal('security_deposit_amount', 15, 2)->nullable()->after('rent_amount');
            $table->string('security_deposit_status')->default('pending')->after('security_deposit_amount');

            $table->string('rent_cycle')->default('annually')->after('security_deposit_status');
            $table->date('next_rent_due_at')->nullable()->after('rent_cycle');

            $table->decimal('commission_percent_override', 5, 2)->nullable()->after('next_rent_due_at');

            $table->unsignedTinyInteger('inspection_cycle_months')->default(6)->after('commission_percent_override');
            $table->date('last_inspection_at')->nullable()->after('inspection_cycle_months');
            $table->string('last_inspection_status')->nullable()->after('last_inspection_at');
            $table->date('next_inspection_at')->nullable()->after('last_inspection_status');

            $table->text('notes')->nullable()->after('next_inspection_at');

            $table->index('next_rent_due_at');
            $table->index('next_inspection_at');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::table('leases', function (Blueprint $table) {
            $table->dropIndex(['next_rent_due_at']);
            $table->dropIndex(['next_inspection_at']);
            $table->dropIndex(['status']);

            $table->dropColumn([
                'security_deposit_amount',
                'security_deposit_status',
                'rent_cycle',
                'next_rent_due_at',
                'commission_percent_override',
                'inspection_cycle_months',
                'last_inspection_at',
                'last_inspection_status',
                'next_inspection_at',
                'notes',
            ]);
        });
    }
};
