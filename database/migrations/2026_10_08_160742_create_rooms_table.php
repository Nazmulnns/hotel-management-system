<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->string('room_number')->unique();

            $table->foreignId('room_type_id')
                ->constrained('room_types')
                ->restrictOnDelete();

            $table->decimal('price_per_night', 10, 2);
            $table->boolean('availability')->default(true);
            $table->unsignedTinyInteger('max_occupancy')->default(2);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
