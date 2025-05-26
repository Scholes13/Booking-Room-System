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
            // Drop the old columns if they exist
            if (Schema::hasColumn('activities', 'activity_date')) {
                $table->dropColumn('activity_date');
            }
            if (Schema::hasColumn('activities', 'start_time')) {
                $table->dropColumn('start_time');
            }
            if (Schema::hasColumn('activities', 'end_time')) {
                $table->dropColumn('end_time');
            }
            
            // Add new datetime columns if they don't exist
            if (!Schema::hasColumn('activities', 'start_datetime')) {
                $table->dateTime('start_datetime')->after('description')->nullable();
            }
            if (!Schema::hasColumn('activities', 'end_datetime')) {
                // Ensure start_datetime exists before trying to place end_datetime after it
                // If start_datetime was just created, this should be fine.
                // If start_datetime already existed, we find a different column to place it after or place it at the end.
                $afterColumn = Schema::hasColumn('activities', 'start_datetime') ? 'start_datetime' : 'description';
                $table->dateTime('end_datetime')->after($afterColumn)->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            // Drop the new columns if they exist
            if (Schema::hasColumn('activities', 'start_datetime')) {
                $table->dropColumn('start_datetime');
            }
            if (Schema::hasColumn('activities', 'end_datetime')) {
                $table->dropColumn('end_datetime');
            }
            
            // Add back the old structure if they don't exist
            if (!Schema::hasColumn('activities', 'activity_date')) {
                $table->date('activity_date')->after('description');
            }
            if (!Schema::hasColumn('activities', 'start_time')) {
                $table->time('start_time')->after('activity_date')->nullable();
            }
            if (!Schema::hasColumn('activities', 'end_time')) {
                $table->time('end_time')->after('start_time')->nullable();
            }
        });
    }
}; 