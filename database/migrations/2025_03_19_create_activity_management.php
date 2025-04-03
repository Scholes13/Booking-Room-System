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
        // Check if activities table doesn't exist before creating it
        if (!Schema::hasTable('activities')) {
            Schema::create('activities', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->foreignId('department_id')->constrained();
                $table->string('activity_type');
                $table->string('province');
                $table->string('city');
                $table->text('description')->nullable();
                $table->dateTime('start_datetime')->nullable();
                $table->dateTime('end_datetime')->nullable();
                $table->timestamps();
            });
        }
        
        // Add booking_type and external_description to bookings table if they don't exist
        if (Schema::hasTable('bookings') && !Schema::hasColumn('bookings', 'booking_type')) {
            Schema::table('bookings', function (Blueprint $table) {
                $table->enum('booking_type', ['internal', 'external'])->default('internal')->after('meeting_room_id');
                $table->text('external_description')->nullable()->after('description');
            });
        }
        
        // Add role to users table if it doesn't exist
        if (Schema::hasTable('users') && !Schema::hasColumn('users', 'role')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('role')->default('admin')->after('remember_token');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Only drop tables/columns that were created in this migration
        if (Schema::hasTable('activities')) {
            Schema::dropIfExists('activities');
        }
        
        if (Schema::hasTable('bookings') && Schema::hasColumn('bookings', 'booking_type')) {
            Schema::table('bookings', function (Blueprint $table) {
                $table->dropColumn(['booking_type', 'external_description']);
            });
        }
        
        if (Schema::hasTable('users') && Schema::hasColumn('users', 'role')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('role');
            });
        }
    }
}; 