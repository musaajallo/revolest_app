<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            if (! Schema::hasColumn('properties', 'purpose')) {
                $table->string('purpose')->default('mixed')->after('type');
            }
        });

        DB::table('properties')
            ->whereNotIn('status', ['active', 'inactive'])
            ->update(['status' => 'inactive']);

        DB::table('properties')
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('listings')
                    ->whereColumn('listings.property_id', 'properties.id');
            })
            ->update(['status' => 'active']);
    }

    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            if (Schema::hasColumn('properties', 'purpose')) {
                $table->dropColumn('purpose');
            }
        });
    }
};
