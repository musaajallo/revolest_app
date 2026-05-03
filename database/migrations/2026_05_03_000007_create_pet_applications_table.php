<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pet_applications', function (Blueprint $table) {
            $table->id();

            $table->foreignId('property_id')->nullable()->constrained('properties')->nullOnDelete();
            $table->foreignId('tenant_id')->nullable()->constrained('tenants')->nullOnDelete();

            $table->string('property_address');
            $table->string('tenant_name');
            $table->string('phone');
            $table->string('email')->nullable();
            $table->date('lease_start_date')->nullable();

            $table->json('pets'); // array of pet objects

            $table->string('keep_location')->nullable(); // indoors/outdoors/both
            $table->boolean('supervised_outdoors')->nullable();
            $table->boolean('past_complaints')->nullable();
            $table->text('past_complaints_notes')->nullable();
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone')->nullable();

            $table->json('details')->nullable();
            $table->text('notes')->nullable();

            $table->string('status')->default('new');
            $table->string('signed_name')->nullable();
            $table->timestamp('signed_at')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamp('submitted_at')->nullable();

            $table->softDeletes();
            $table->timestamps();

            $table->index('status');
            $table->index('email');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pet_applications');
    }
};
