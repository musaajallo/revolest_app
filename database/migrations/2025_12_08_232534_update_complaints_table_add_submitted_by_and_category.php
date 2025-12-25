<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('complaints', function (Blueprint $table) {
            // Add submitted_by_user_id to track who created the complaint
            $table->foreignId('submitted_by_user_id')->after('id')->constrained('users')->onDelete('cascade');

            // Make tenant_id nullable since property complaints may not involve a specific tenant
            $table->foreignId('tenant_id')->nullable()->change();

            // Add complaint category/type
            $table->string('complaint_category')->after('description')->default('general');

            // Add priority field
            $table->string('priority')->after('complaint_category')->default('medium');

            // Add resolution notes
            $table->text('resolution_notes')->after('resolved_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('complaints', function (Blueprint $table) {
            // Drop new columns
            $table->dropForeign(['submitted_by_user_id']);
            $table->dropColumn('submitted_by_user_id');
            $table->dropColumn('complaint_category');
            $table->dropColumn('priority');
            $table->dropColumn('resolution_notes');

            // Make tenant_id required again (note: this may fail if there are null values)
            $table->foreignId('tenant_id')->nullable(false)->change();
        });
    }
};
