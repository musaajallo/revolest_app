<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('built_property_listing_leads', function (Blueprint $table) {
            $table->decimal('budget_min', 15, 2)->nullable()->after('asking_price');
            $table->decimal('budget_max', 15, 2)->nullable()->after('budget_min');
            $table->unsignedTinyInteger('bedrooms')->nullable()->after('bedrooms_detail');
            $table->unsignedTinyInteger('bathrooms')->nullable()->after('bathrooms_detail');
            $table->boolean('furnished')->nullable()->after('bathrooms');
            $table->string('property_condition')->nullable()->after('property_status');
            $table->string('intended_use')->nullable()->after('property_condition');
            $table->string('plot_size')->nullable()->after('intended_use');
            $table->string('referred_by_name')->nullable()->after('referral_name');
        });
    }

    public function down(): void
    {
        Schema::table('built_property_listing_leads', function (Blueprint $table) {
            $table->dropColumn([
                'budget_min', 'budget_max', 'bedrooms', 'bathrooms', 'furnished',
                'property_condition', 'intended_use', 'plot_size', 'referred_by_name',
            ]);
        });
    }
};
