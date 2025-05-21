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
        Schema::table('company_divisions', function (Blueprint $table) {
            // Check if description column exists
            if (Schema::hasColumn('company_divisions', 'description')) {
                // If using MySQL 8.0+, we can rename the column
                // $table->renameColumn('description', 'notes');
                
                // For compatibility with all DB systems, drop and add
                $table->dropColumn('description');
                $table->text('notes')->nullable()->after('name');
            } else if (!Schema::hasColumn('company_divisions', 'notes')) {
                // If neither column exists, add the notes column
                $table->text('notes')->nullable()->after('name');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('company_divisions', function (Blueprint $table) {
            if (Schema::hasColumn('company_divisions', 'notes')) {
                $table->dropColumn('notes');
                $table->string('description')->nullable()->after('name');
            }
        });
    }
}; 