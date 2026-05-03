<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('land_sale_leads', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('email')->nullable();
            $table->string('phone_primary');
            $table->string('phone_secondary')->nullable();
            $table->string('phone_tertiary')->nullable();
            $table->string('current_address')->nullable();

            $table->text('land_location')->nullable();
            $table->string('land_size')->nullable();
            $table->string('current_use')->nullable();
            $table->string('current_use_other')->nullable();
            $table->boolean('jointly_owned')->nullable();
            $table->boolean('ownership_disputes')->nullable();
            $table->string('zoning')->nullable();
            $table->decimal('asking_price', 15, 2)->nullable();

            $table->json('consultation_purpose')->nullable();
            $table->string('consultation_purpose_other')->nullable();
            $table->text('plans_for_land')->nullable();
            $table->text('current_issues')->nullable();

            $table->boolean('has_liens')->nullable();
            $table->boolean('taxes_up_to_date')->nullable();
            $table->boolean('has_legal_documents')->nullable();
            $table->json('documents_provided')->nullable();
            $table->boolean('free_from_disputes')->nullable();

            $table->json('utilities')->nullable();
            $table->boolean('road_accessible')->nullable();
            $table->text('existing_structures')->nullable();
            $table->text('environmental_concerns')->nullable();
            $table->boolean('has_recent_survey')->nullable();
            $table->text('land_history')->nullable();

            $table->string('referral_source')->nullable();
            $table->text('referral_notes')->nullable();
            $table->boolean('previous_company_contact')->nullable();
            $table->text('previous_company_experience')->nullable();

            $table->json('details')->nullable();
            $table->text('notes')->nullable();

            $table->string('status')->default('new');
            $table->foreignId('agent_id')->nullable()->constrained('agents')->nullOnDelete();
            $table->foreignId('converted_property_id')->nullable()->constrained('properties')->nullOnDelete();
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
        Schema::dropIfExists('land_sale_leads');
    }
};
