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
        // Add booking_type and external_description to bookings table if they don't exist
        if (Schema::hasTable('bookings')) {
            if (!Schema::hasColumn('bookings', 'booking_type')) {
                Schema::table('bookings', function (Blueprint $table) {
                    $table->enum('booking_type', ['internal', 'external'])->default('internal')->after('meeting_room_id');
                });
            }
            
            if (!Schema::hasColumn('bookings', 'external_description')) {
                Schema::table('bookings', function (Blueprint $table) {
                    $table->text('external_description')->nullable()->after('description');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('bookings')) {
            Schema::table('bookings', function (Blueprint $table) {
                if (Schema::hasColumn('bookings', 'booking_type')) {
                    $table->dropColumn('booking_type');
                }
                
                if (Schema::hasColumn('bookings', 'external_description')) {
                    $table->dropColumn('external_description');
                }
            });
        }
    }
}; 