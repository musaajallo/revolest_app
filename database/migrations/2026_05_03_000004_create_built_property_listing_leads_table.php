<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('built_property_listing_leads', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('nationality')->nullable();
            $table->string('email')->nullable();
            $table->string('phone');
            $table->string('street_address')->nullable();
            $table->string('city')->nullable();
            $table->string('region')->nullable();

            $table->text('legal_description')->nullable();
            $table->string('property_address');
            $table->string('land_dimension')->nullable();
            $table->string('approximate_sqft')->nullable();
            $table->string('property_status')->nullable();
            $table->string('property_type')->nullable();
            $table->json('buildings_on_property')->nullable();
            $table->decimal('asking_price', 15, 2)->nullable();
            $table->string('possession')->nullable();

            $table->string('showing_instructions')->nullable();
            $table->unsignedSmallInteger('number_of_rooms')->nullable();
            $table->string('bedrooms_detail')->nullable();
            $table->string('bathrooms_detail')->nullable();
            $table->string('age_of_house')->nullable();
            $table->string('square_footage')->nullable();
            $table->string('roof_type')->nullable();
            $table->string('furnace')->nullable();
            $table->text('amenities')->nullable();
            $table->json('natural_features')->nullable();
            $table->json('site_documents')->nullable();
            $table->json('disclosures')->nullable();
            $table->text('disclosures_other')->nullable();
            $table->json('documents_attached')->nullable();

            $table->string('referral_source')->nullable();
            $table->string('referral_name')->nullable();
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
        Schema::dropIfExists('built_property_listing_leads');
    }
};
