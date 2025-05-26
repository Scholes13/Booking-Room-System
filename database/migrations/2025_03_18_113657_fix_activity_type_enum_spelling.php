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
        // Only update existing records with the misspelled value
        // We will not change the column type to ENUM to allow for more flexible activity types
        DB::statement("UPDATE activities SET activity_type = 'Invitation' WHERE activity_type = 'Invititation'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // If you need to revert, you might want to change 'Invitation' back to 'Invititation'
        // However, it's generally better to keep the corrected spelling.
        // DB::statement("UPDATE activities SET activity_type = 'Invititation' WHERE activity_type = 'Invitation'");
    }
};
