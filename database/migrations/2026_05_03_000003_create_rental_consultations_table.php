<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rental_consultations', function (Blueprint $table) {
            $table->id();
            $table->date('consultation_date')->nullable();

            $table->string('full_name');
            $table->string('address')->nullable();
            $table->string('nationality')->nullable();
            $table->string('occupation')->nullable();
            $table->string('institution')->nullable();
            $table->string('marital_status')->nullable();
            $table->unsignedSmallInteger('number_of_kids')->nullable();
            $table->string('phone');
            $table->string('email')->nullable();

            $table->string('preferred_areas')->nullable();
            $table->string('property_kind')->nullable();
            $table->unsignedTinyInteger('bedrooms')->nullable();
            $table->boolean('furnished')->nullable();
            $table->string('preferred_structure')->nullable();
            $table->text('desired_facilities')->nullable();
            $table->text('property_suggestions')->nullable();
            $table->text('reason_for_moving')->nullable();

            $table->unsignedSmallInteger('occupants_count')->nullable();
            $table->string('move_in_window')->nullable();
            $table->string('rental_duration')->nullable();
            $table->string('payment_plan')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('payer')->nullable();
            $table->string('payer_name')->nullable();
            $table->string('payer_occupation')->nullable();
            $table->string('payer_address')->nullable();
            $table->string('payer_phone')->nullable();
            $table->string('payer_relationship')->nullable();

            $table->boolean('previous_company_contact')->nullable();
            $table->text('previous_company_experience')->nullable();
            $table->string('referral_source')->nullable();
            $table->string('referral_name')->nullable();

            $table->json('details')->nullable();
            $table->text('notes')->nullable();

            $table->string('status')->default('new');
            $table->foreignId('agent_id')->nullable()->constrained('agents')->nullOnDelete();
            $table->foreignId('tenant_id')->nullable()->constrained('tenants')->nullOnDelete();
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
        Schema::dropIfExists('rental_consultations');
    }
};
