<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Create temporary table with old structure
        Schema::create('temp_bookings', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('department');
            $table->date('date');
            $table->time('start_time');
            $table->time('end_time');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('meeting_room_id');
            $table->timestamps();
        });

        // 2. Drop existing bookings table
        Schema::dropIfExists('bookings');

        // 3. Create new bookings table with complete structure
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('department');
            $table->foreignId('meeting_room_id')->constrained();
            $table->enum('booking_type', ['internal', 'external'])->default('internal');
            $table->date('date');
            $table->time('start_time');
            $table->time('end_time');
            $table->text('description')->nullable();
            $table->text('external_description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('temp_bookings');
        Schema::dropIfExists('bookings');
    }
};
