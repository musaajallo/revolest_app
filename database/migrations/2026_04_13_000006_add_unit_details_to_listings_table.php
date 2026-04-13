<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            if (! Schema::hasColumn('listings', 'area')) {
                $table->unsignedInteger('area')->nullable()->after('bathrooms');
            }

            if (! Schema::hasColumn('listings', 'guest_toilets')) {
                $table->unsignedTinyInteger('guest_toilets')->default(0)->after('area');
            }
        });

        // Backfill from legacy boolean field if present.
        if (Schema::hasColumn('listings', 'has_guest_toilet')) {
            DB::table('listings')
                ->where('has_guest_toilet', true)
                ->update(['guest_toilets' => 1]);
        }
    }

    public function down(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            if (Schema::hasColumn('listings', 'guest_toilets')) {
                $table->dropColumn('guest_toilets');
            }

            if (Schema::hasColumn('listings', 'area')) {
                $table->dropColumn('area');
            }
        });
    }
};
