<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rental_consultations', function (Blueprint $table) {
            $table->renameColumn('preferred_areas', 'preferred_locations');
        });

        Schema::table('rental_consultations', function (Blueprint $table) {
            $table->decimal('budget_min', 15, 2)->nullable()->after('rental_duration');
            $table->decimal('budget_max', 15, 2)->nullable()->after('budget_min');
            $table->unsignedTinyInteger('bathrooms')->nullable()->after('bedrooms');
            $table->string('property_condition')->nullable()->after('preferred_structure');
            $table->string('intended_use')->nullable()->after('property_condition');
            $table->string('plot_size')->nullable()->after('intended_use');
            $table->string('referred_by_name')->nullable()->after('referral_name');
        });
    }

    public function down(): void
    {
        Schema::table('rental_consultations', function (Blueprint $table) {
            $table->dropColumn([
                'budget_min', 'budget_max', 'bathrooms',
                'property_condition', 'intended_use', 'plot_size', 'referred_by_name',
            ]);
        });

        Schema::table('rental_consultations', function (Blueprint $table) {
            $table->renameColumn('preferred_locations', 'preferred_areas');
        });
    }
};
