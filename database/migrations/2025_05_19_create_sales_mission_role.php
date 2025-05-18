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
        // Add sales_mission role constant to User model
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'superadmin', 'admin_bas', 'sales_mission') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to original enum without sales_mission
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'superadmin', 'admin_bas') NOT NULL");
    }
}; 