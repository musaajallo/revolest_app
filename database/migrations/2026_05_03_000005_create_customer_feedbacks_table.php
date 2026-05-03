<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customer_feedbacks', function (Blueprint $table) {
            $table->id();

            $table->string('full_name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();

            $table->string('overall_satisfaction')->nullable();
            $table->string('service_quality')->nullable();
            $table->string('customer_service_experience')->nullable();
            $table->string('staff_helpful')->nullable();
            $table->string('delivery_on_time')->nullable();
            $table->string('ease_of_finding')->nullable();
            $table->string('would_recommend')->nullable();
            $table->string('accessibility_score')->nullable();
            $table->string('expectations_met')->nullable();
            $table->string('brand_score')->nullable();

            $table->string('heard_about_us')->nullable();
            $table->string('heard_about_us_other')->nullable();

            $table->text('improvement_suggestions')->nullable();
            $table->text('additional_comments')->nullable();
            $table->text('why_chose_us')->nullable();
            $table->text('missing_features')->nullable();

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
        Schema::dropIfExists('customer_feedbacks');
    }
};
