<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMeetingRoomsTable extends Migration
{
    public function up()
    {
        Schema::create('meeting_rooms', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nama ruangan
            $table->text('description')->nullable(); // Deskripsi atau fasilitas ruangan
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('meeting_rooms');
    }
}
