<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payment_records', function (Blueprint $table) {
            $table->id();
            $table->string('invoice')->unique(); // Tambahan kolom noinvoice
            $table->foreignId('medical_record_id')->constrained()->onDelete('cascade');
            $table->string('total_amount');
            $table->date('payment_date');
            $table->enum('status', ['unpaid', 'paid'])->default('unpaid');
            $table->timestamps();
        });

        Schema::create('medication_payment', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_record_id')->constrained()->onDelete('cascade');
            $table->foreignId('medication_id')->constrained()->onDelete('cascade');
            $table->integer('quantity');
            $table->string('price');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medication_payment');
        Schema::dropIfExists('payment_records');
    }
};
