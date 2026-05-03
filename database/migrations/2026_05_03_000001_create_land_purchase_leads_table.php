<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('land_purchase_leads', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('email')->nullable();
            $table->string('phone');
            $table->string('address')->nullable();
            $table->string('identification_type')->nullable();
            $table->string('identification_number')->nullable();
            $table->boolean('id_attached')->default(false);

            $table->string('preferred_locations')->nullable();
            $table->string('plot_size')->nullable();
            $table->string('with_buildings')->nullable();
            $table->boolean('future_development')->nullable();
            $table->string('land_type')->nullable();

            $table->string('budget')->nullable();
            $table->string('payment_plan')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('timeframe')->nullable();
            $table->string('completion_target')->nullable();

            $table->json('details')->nullable();
            $table->text('notes')->nullable();
            $table->text('special_requirements')->nullable();

            $table->string('status')->default('new');
            $table->foreignId('agent_id')->nullable()->constrained('agents')->nullOnDelete();
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
        Schema::dropIfExists('land_purchase_leads');
    }
};
