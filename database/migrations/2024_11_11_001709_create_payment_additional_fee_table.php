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
        Schema::create('payment_additional_fee', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_record_id')->constrained()->cascadeOnDelete();
            $table->foreignId('additional_fees_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_additional_fee');
    }
};
