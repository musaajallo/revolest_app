<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_build_property_leads', function (Blueprint $table) {
            $table->id();

            $table->string('full_name');
            $table->string('email')->nullable();
            $table->string('phone_primary');
            $table->string('phone_secondary')->nullable();
            $table->string('phone_tertiary')->nullable();
            $table->string('current_address')->nullable();

            $table->string('property_type')->nullable();
            $table->string('build_status')->nullable();
            $table->string('preferred_location')->nullable();
            $table->text('avoid_areas')->nullable();
            $table->string('architectural_style')->nullable();
            $table->string('bedrooms_bathrooms')->nullable();
            $table->text('special_features')->nullable();
            $table->text('luxury_features')->nullable();

            $table->string('budget')->nullable();
            $table->string('financing_method')->nullable();
            $table->string('mortgage_preapproval')->nullable();
            $table->boolean('needs_mortgage_advice')->nullable();
            $table->boolean('open_to_negotiation')->nullable();

            $table->string('min_square_footage')->nullable();
            $table->boolean('needs_extra_space')->nullable();
            $table->string('lot_size_preference')->nullable();
            $table->string('storey_preference')->nullable();
            $table->text('layout_preference')->nullable();

            $table->text('proximity_preference')->nullable();
            $table->string('area_kind')->nullable();
            $table->text('amenities_importance')->nullable();
            $table->string('community_type')->nullable();
            $table->text('landmarks')->nullable();

            $table->string('move_in_target')->nullable();
            $table->text('time_sensitivity')->nullable();
            $table->string('readiness_preference')->nullable();

            $table->string('use_purpose')->nullable();
            $table->text('long_term_value')->nullable();
            $table->boolean('open_to_developments')->nullable();

            $table->text('legal_requirements')->nullable();
            $table->boolean('needs_inspection_help')->nullable();

            $table->string('maintenance_effort')->nullable();
            $table->string('maintenance_preference')->nullable();
            $table->text('additional_services')->nullable();

            $table->string('household_type')->nullable();
            $table->text('accessibility_needs')->nullable();
            $table->text('pet_accommodations')->nullable();

            $table->string('eco_priority')->nullable();
            $table->boolean('smart_home_interest')->nullable();

            $table->boolean('customizable_required')->nullable();
            $table->boolean('needs_reno_design_help')->nullable();
            $table->text('resale_plan')->nullable();

            $table->string('property_age_preference')->nullable();
            $table->string('turnkey_preference')->nullable();
            $table->text('other_considerations')->nullable();

            $table->boolean('previous_company_contact')->nullable();
            $table->text('previous_company_experience')->nullable();
            $table->string('referral_source')->nullable();
            $table->string('referral_name')->nullable();

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
        Schema::dropIfExists('purchase_build_property_leads');
    }
};
