<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 'appointment_id',
     * 'complaint',
     * 'diagnosis',
     * 'physical_exam',
     * 'recommendation',
     */
    public function up(): void
    {
        Schema::create('medical_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('appointment_id')->constrained('appointments')->cascadeOnDelete();
            $table->text('complaint');
            $table->text('diagnosis')->nullable();
            $table->text('physical_exam')->nullable();
            $table->text('recommendation')->nullable();
            $table->enum('type', ['outpatient', 'inpatient']);
            $table->enum('status', ['in-progress', 'follow-up', 'payment', 'completed'])->default('in-progress');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medical_records');
    }
};
