<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->decimal('sale_price', 15, 2)->nullable()->after('price');
            $table->decimal('rental_price', 15, 2)->nullable()->after('sale_price');
            $table->decimal('security_deposit', 15, 2)->nullable()->after('rental_price');
            $table->decimal('agent_fee', 15, 2)->nullable()->after('security_deposit');
        });

        DB::table('properties')
            ->where('purpose', 'sale')
            ->whereNotNull('price')
            ->update(['sale_price' => DB::raw('price')]);

        DB::table('properties')
            ->where('purpose', 'rent')
            ->whereNotNull('price')
            ->update(['rental_price' => DB::raw('price')]);
    }

    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropColumn([
                'sale_price',
                'rental_price',
                'security_deposit',
                'agent_fee',
            ]);
        });
    }
};
