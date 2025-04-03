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
        Schema::table('activities', function (Blueprint $table) {
            // Drop the old columns
            $table->dropColumn(['activity_date', 'start_time', 'end_time']);
            
            // Add new datetime columns
            $table->dateTime('start_datetime')->after('description')->nullable();
            $table->dateTime('end_datetime')->after('start_datetime')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            // Drop the new columns
            $table->dropColumn(['start_datetime', 'end_datetime']);
            
            // Add back the old structure
            $table->date('activity_date')->after('description');
            $table->time('start_time')->after('activity_date')->nullable();
            $table->time('end_time')->after('start_time')->nullable();
        });
    }
}; 