<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add unique_id column to agents
        Schema::table('agents', function (Blueprint $table) {
            $table->string('unique_id')->unique()->nullable()->after('id');
        });

        // Add unique_id column to owners
        Schema::table('owners', function (Blueprint $table) {
            $table->string('unique_id')->unique()->nullable()->after('id');
        });

        // Add unique_id column to tenants
        Schema::table('tenants', function (Blueprint $table) {
            $table->string('unique_id')->unique()->nullable()->after('id');
        });

        // Generate IDs for existing records
        $agents = DB::table('agents')->get();
        foreach ($agents as $agent) {
            DB::table('agents')->where('id', $agent->id)->update([
                'unique_id' => 'AGT-' . str_pad($agent->id, 4, '0', STR_PAD_LEFT)
            ]);
        }

        $owners = DB::table('owners')->get();
        foreach ($owners as $owner) {
            DB::table('owners')->where('id', $owner->id)->update([
                'unique_id' => 'OWN-' . str_pad($owner->id, 4, '0', STR_PAD_LEFT)
            ]);
        }

        $tenants = DB::table('tenants')->get();
        foreach ($tenants as $tenant) {
            DB::table('tenants')->where('id', $tenant->id)->update([
                'unique_id' => 'TNT-' . str_pad($tenant->id, 4, '0', STR_PAD_LEFT)
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('agents', function (Blueprint $table) {
            $table->dropColumn('unique_id');
        });

        Schema::table('owners', function (Blueprint $table) {
            $table->dropColumn('unique_id');
        });

        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn('unique_id');
        });
    }
};
