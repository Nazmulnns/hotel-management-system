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
        Schema::create('applied_discounts', function (Blueprint $table) {
            $table->foreignId('booking_id')->constrained()->restrictOnDelete();
            $table->foreignId('discount_id')->constrained()->restrictOnDelete();
            $table->decimal('amount_saved', 10, 2)->default(0);
            $table->primary(['booking_id', 'discount_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applied_discounts');
    }
};
