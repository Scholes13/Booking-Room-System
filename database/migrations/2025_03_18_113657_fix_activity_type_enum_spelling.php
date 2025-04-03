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
        // First, modify the enum constraint to accept the correct spelling
        DB::statement("ALTER TABLE activities MODIFY COLUMN activity_type ENUM('Meeting', 'Invitation', 'Survey')");
        
        // Then update any existing records with the misspelled value
        DB::statement("UPDATE activities SET activity_type = 'Invitation' WHERE activity_type = 'Invititation'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to the original enum (though this is not recommended)
        DB::statement("ALTER TABLE activities MODIFY COLUMN activity_type ENUM('Meeting', 'Invititation', 'Survey')");
    }
};
