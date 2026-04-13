<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            if (! Schema::hasColumn('properties', 'is_featured')) {
                $table->boolean('is_featured')->default(false)->after('status');
            }

            if (! Schema::hasColumn('properties', 'available_from')) {
                $table->timestamp('available_from')->nullable()->after('is_featured');
            }

            if (! Schema::hasColumn('properties', 'is_storey_building')) {
                $table->boolean('is_storey_building')->default(false)->after('available_from');
            }

            if (! Schema::hasColumn('properties', 'number_of_storeys')) {
                $table->unsignedTinyInteger('number_of_storeys')->nullable()->after('is_storey_building');
            }
        });

        Schema::table('listings', function (Blueprint $table) {
            if (! Schema::hasColumn('listings', 'floor_number')) {
                $table->unsignedTinyInteger('floor_number')->default(0)->after('unit_name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            if (Schema::hasColumn('listings', 'floor_number')) {
                $table->dropColumn('floor_number');
            }
        });

        Schema::table('properties', function (Blueprint $table) {
            foreach (['is_featured', 'available_from', 'is_storey_building', 'number_of_storeys'] as $column) {
                if (Schema::hasColumn('properties', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
