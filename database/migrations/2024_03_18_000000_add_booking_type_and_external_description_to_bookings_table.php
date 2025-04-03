<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->enum('booking_type', ['internal', 'external'])->default('internal')->after('meeting_room_id');
            $table->text('external_description')->nullable()->after('description');
        });
    }

    public function down()
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn('booking_type');
            $table->dropColumn('external_description');
        });
    }
}; 