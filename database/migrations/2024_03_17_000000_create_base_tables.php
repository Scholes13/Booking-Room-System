<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Create departments table
        if (!Schema::hasTable('departments')) {
            Schema::create('departments', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->timestamps();
            });
        }

        // Create meeting_rooms table
        if (!Schema::hasTable('meeting_rooms')) {
            Schema::create('meeting_rooms', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }

        // Create employees table
        if (!Schema::hasTable('employees')) {
            Schema::create('employees', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->foreignId('department_id')->constrained();
                $table->timestamps();
            });
        }

        // Create bookings table
        if (!Schema::hasTable('bookings')) {
            Schema::create('bookings', function (Blueprint $table) {
                $table->id();
                $table->string('nama');
                $table->string('department');
                $table->foreignId('meeting_room_id')->constrained();
                $table->date('date');
                $table->time('start_time');
                $table->time('end_time');
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }

        // Create activities table
        if (!Schema::hasTable('activities')) {
            Schema::create('activities', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->foreignId('department_id')->constrained();
                $table->string('activity_type');
                $table->string('province');
                $table->string('city');
                $table->text('description')->nullable();
                $table->date('activity_date');
                $table->time('start_time')->nullable();
                $table->time('end_time')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('activities');
        Schema::dropIfExists('bookings');
        Schema::dropIfExists('employees');
        Schema::dropIfExists('meeting_rooms');
        Schema::dropIfExists('departments');
    }
}; 