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
        Schema::table('activities', function (Blueprint $table) {
            // Check if the status column doesn't exist
            if (!Schema::hasColumn('activities', 'status')) {
                $table->string('status')->nullable()->after('end_datetime');
            }
        });

        // Reset the migration in the migrations table for the previous status column migration
        DB::table('migrations')
            ->where('migration', '2025_05_16_183437_add_status_column_to_activities_table')
            ->delete();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            if (Schema::hasColumn('activities', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};
