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
        Schema::table('employees', function (Blueprint $table) {
            if (!Schema::hasColumn('employees', 'phone')) {
                if (Schema::hasColumn('employees', 'position')) {
                    $table->string('phone')->nullable()->after('position');
                } else {
                    $table->string('phone')->nullable();
                }
            }
            
            if (!Schema::hasColumn('employees', 'email')) {
                // Determine the column to place 'email' after
                // If 'phone' exists, place it after 'phone'. Otherwise, try 'position', or just add it.
                $afterColumn = 'position'; // Default
                if (Schema::hasColumn('employees', 'phone')) {
                    $afterColumn = 'phone';
                } elseif (!Schema::hasColumn('employees', 'position')){
                    // If neither phone nor position exists, just add the column (it will be at the end)
                     $table->string('email')->nullable();
                     return; // Exit early as email is added
                }
                $table->string('email')->nullable()->after($afterColumn);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            if (Schema::hasColumn('employees', 'phone')) {
                $table->dropColumn('phone');
            }
            if (Schema::hasColumn('employees', 'email')) {
                $table->dropColumn('email');
            }
        });
    }
};
