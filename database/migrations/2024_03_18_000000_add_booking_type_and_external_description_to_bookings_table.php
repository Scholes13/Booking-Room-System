<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('bookings', 'booking_type')) {
                $table->enum('booking_type', ['internal', 'external'])->default('internal')->after('meeting_room_id');
            }
            if (!Schema::hasColumn('bookings', 'external_description')) {
                $table->text('external_description')->nullable()->after('description');
            }
        });
    }

    public function down()
    {
        Schema::table('bookings', function (Blueprint $table) {
            if (Schema::hasColumn('bookings', 'booking_type')) {
                $table->dropColumn('booking_type');
            }
            if (Schema::hasColumn('bookings', 'external_description')) {
                $table->dropColumn('external_description');
            }
        });
    }
}; 