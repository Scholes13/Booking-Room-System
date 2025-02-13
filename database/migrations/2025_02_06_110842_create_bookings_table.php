<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookingsTable extends Migration
{
    public function up()
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('department');
            $table->date('date');
            $table->time('start_time');
            $table->time('end_time');
            $table->text('description')->nullable();
            // Relasi ke meeting_rooms
            $table->foreignId('meeting_room_id')->constrained('meeting_rooms')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('bookings');
    }
}
