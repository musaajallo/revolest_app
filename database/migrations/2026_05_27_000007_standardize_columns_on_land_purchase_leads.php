<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('land_purchase_leads', function (Blueprint $table) {
            $table->decimal('budget_min', 15, 2)->nullable()->after('budget');
            $table->decimal('budget_max', 15, 2)->nullable()->after('budget_min');
            $table->unsignedTinyInteger('bathrooms')->nullable()->after('plot_size');
            $table->string('property_condition')->nullable()->after('bathrooms');
            $table->string('intended_use')->nullable()->after('property_condition');
            $table->string('referred_by_name')->nullable()->after('agent_id');
        });
    }

    public function down(): void
    {
        Schema::table('land_purchase_leads', function (Blueprint $table) {
            $table->dropColumn([
                'budget_min', 'budget_max', 'bathrooms',
                'property_condition', 'intended_use', 'referred_by_name',
            ]);
        });
    }
};
