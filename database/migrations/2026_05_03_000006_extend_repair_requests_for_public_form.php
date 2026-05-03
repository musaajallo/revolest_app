<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop NOT NULL on existing FKs so walk-in submissions (no logged-in tenant
        // and no linked property yet) can land. Admins link the FKs during triage.
        Schema::table('repair_requests', function (Blueprint $table) {
            $table->foreignId('property_id')->nullable()->change();
            $table->foreignId('tenant_id')->nullable()->change();
            $table->timestamp('submitted_at')->nullable()->change();
        });

        Schema::table('repair_requests', function (Blueprint $table) {
            $table->string('first_name')->nullable()->after('tenant_id');
            $table->string('last_name')->nullable()->after('first_name');
            $table->string('email')->nullable()->after('last_name');
            $table->string('phone')->nullable()->after('email');
            $table->string('property_address')->nullable()->after('phone');
            $table->string('apartment_number')->nullable()->after('property_address');

            $table->string('priority')->nullable()->after('description');
            $table->string('category')->nullable()->after('priority');
            $table->string('preferred_visit')->nullable()->after('category');
            $table->boolean('has_pets')->nullable()->after('preferred_visit');
            $table->text('pet_notes')->nullable()->after('has_pets');
            $table->boolean('permission_to_enter')->default(false)->after('pet_notes');

            $table->string('tenant_signature_name')->nullable()->after('permission_to_enter');
            $table->timestamp('signed_at')->nullable()->after('tenant_signature_name');
            $table->string('ip_address', 45)->nullable()->after('signed_at');
            $table->string('user_agent')->nullable()->after('ip_address');

            $table->timestamp('completed_at')->nullable()->after('resolved_at');
            $table->string('completed_by_name')->nullable()->after('completed_at');
            $table->text('completion_notes')->nullable()->after('completed_by_name');

            $table->index('priority');
        });
    }

    public function down(): void
    {
        Schema::table('repair_requests', function (Blueprint $table) {
            $table->dropIndex(['priority']);
            $table->dropColumn([
                'first_name', 'last_name', 'email', 'phone', 'property_address', 'apartment_number',
                'priority', 'category', 'preferred_visit', 'has_pets', 'pet_notes', 'permission_to_enter',
                'tenant_signature_name', 'signed_at', 'ip_address', 'user_agent',
                'completed_at', 'completed_by_name', 'completion_notes',
            ]);
        });

        // Restoring NOT NULL constraints on FKs is intentionally skipped here:
        // existing rows may now have nulls. Manual cleanup required before reverting.
    }
};
