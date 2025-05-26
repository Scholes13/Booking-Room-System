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
        Schema::table('teams', function (Blueprint $table) {
            // Check if the column exists before trying to drop it
            if (Schema::hasColumn('teams', 'members')) {
                $table->dropColumn('members');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('teams', function (Blueprint $table) {
            // If you want to be able to roll back, you can re-add the column here.
            // For now, we assume it's not needed.
            // $table->json('members')->nullable()->after('description'); 
        });
    }
};
