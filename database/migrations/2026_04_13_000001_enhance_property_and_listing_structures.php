<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        Schema::table('properties', function (Blueprint $table) {
            $table->string('listing_category')->default('building')->after('type');
            $table->boolean('is_for_sale')->default(false)->after('listing_category');
            $table->boolean('is_for_rent')->default(true)->after('is_for_sale');
            $table->unsignedInteger('units_count')->default(1)->after('owner_id');
        });

        if ($driver !== 'sqlite') {
            Schema::table('listings', function (Blueprint $table) {
                $table->dropForeign(['agent_id']);
            });

            if ($driver === 'mysql') {
                DB::statement('ALTER TABLE listings MODIFY agent_id BIGINT UNSIGNED NULL');
            } elseif ($driver === 'pgsql') {
                DB::statement('ALTER TABLE listings ALTER COLUMN agent_id DROP NOT NULL');
            }
        }

        Schema::table('listings', function (Blueprint $table) use ($driver) {
            if ($driver !== 'sqlite') {
                $table->foreign('agent_id')->references('id')->on('agents')->nullOnDelete();
            }

            $table->string('unit_name')->nullable()->after('agent_id');
            $table->decimal('security_deposit', 15, 2)->nullable()->after('price');
            $table->decimal('agent_fee', 15, 2)->nullable()->after('security_deposit');
            $table->unsignedTinyInteger('bedrooms')->nullable()->after('status');
            $table->unsignedTinyInteger('bathrooms')->nullable()->after('bedrooms');
            $table->boolean('has_dining_area')->default(false)->after('bathrooms');
            $table->unsignedTinyInteger('boys_quarters')->nullable()->after('has_dining_area');
            $table->unsignedTinyInteger('kitchens')->nullable()->after('boys_quarters');
            $table->boolean('has_guest_toilet')->default(false)->after('kitchens');
            $table->json('amenities')->nullable()->after('has_guest_toilet');
            $table->longText('description')->nullable()->after('amenities');
            $table->boolean('listed_by_company')->default(false)->after('description');
        });

        DB::table('properties')->where('status', 'available')->update(['status' => 'for_rent']);
        DB::table('listings')->where('status', 'active')->update(['status' => 'for_rent']);

        DB::table('properties')->where('type', 'land')->update([
            'listing_category' => 'land',
            'units_count' => 0,
        ]);
    }

    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver !== 'sqlite') {
            Schema::table('listings', function (Blueprint $table) {
                $table->dropForeign(['agent_id']);
            });

            if ($driver === 'mysql') {
                DB::statement('ALTER TABLE listings MODIFY agent_id BIGINT UNSIGNED NOT NULL');
            } elseif ($driver === 'pgsql') {
                DB::statement('ALTER TABLE listings ALTER COLUMN agent_id SET NOT NULL');
            }
        }

        Schema::table('listings', function (Blueprint $table) use ($driver) {
            if ($driver !== 'sqlite') {
                $table->foreign('agent_id')->references('id')->on('agents')->onDelete('cascade');
            }

            $table->dropColumn([
                'unit_name',
                'security_deposit',
                'agent_fee',
                'bedrooms',
                'bathrooms',
                'has_dining_area',
                'boys_quarters',
                'kitchens',
                'has_guest_toilet',
                'amenities',
                'description',
                'listed_by_company',
            ]);
        });

        Schema::table('properties', function (Blueprint $table) {
            $table->dropColumn([
                'listing_category',
                'is_for_sale',
                'is_for_rent',
                'units_count',
            ]);
        });
    }
};
