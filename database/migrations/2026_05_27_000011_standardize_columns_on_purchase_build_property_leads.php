<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchase_build_property_leads', function (Blueprint $table) {
            $table->decimal('budget_min', 15, 2)->nullable()->after('budget');
            $table->decimal('budget_max', 15, 2)->nullable()->after('budget_min');
            $table->unsignedTinyInteger('bedrooms')->nullable()->after('bedrooms_bathrooms');
            $table->unsignedTinyInteger('bathrooms')->nullable()->after('bedrooms');
            $table->string('property_condition')->nullable()->after('build_status');
            $table->string('intended_use')->nullable()->after('property_condition');
            $table->string('plot_size')->nullable()->after('intended_use');
            $table->string('referred_by_name')->nullable()->after('referral_name');
        });
    }

    public function down(): void
    {
        Schema::table('purchase_build_property_leads', function (Blueprint $table) {
            $table->dropColumn([
                'budget_min', 'budget_max', 'bedrooms', 'bathrooms',
                'property_condition', 'intended_use', 'plot_size', 'referred_by_name',
            ]);
        });
    }
};
