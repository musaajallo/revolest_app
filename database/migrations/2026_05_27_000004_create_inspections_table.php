<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inspections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lease_id')->constrained('leases')->cascadeOnDelete();
            $table->foreignId('property_id')->constrained('properties')->cascadeOnDelete();
            $table->foreignId('inspector_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('inspected_at');
            $table->string('status');
            $table->text('findings')->nullable();
            $table->date('next_inspection_due_at')->nullable();
            $table->json('images')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('lease_id');
            $table->index('status');
            $table->index('inspected_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inspections');
    }
};
