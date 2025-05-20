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
        // SAFE migration - only adds new columns if table exists
        if (Schema::hasTable('sales_officer_activities')) {
            // Check if columns don't already exist to avoid errors
            if (!Schema::hasColumn('sales_officer_activities', 'next_follow_up')) {
                Schema::table('sales_officer_activities', function (Blueprint $table) {
                    $table->dateTime('next_follow_up')->nullable()->after('products_discussed');
                });
            }
            
            if (!Schema::hasColumn('sales_officer_activities', 'follow_up_type')) {
                Schema::table('sales_officer_activities', function (Blueprint $table) {
                    $table->string('follow_up_type')->nullable()->after('next_follow_up');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('sales_officer_activities')) {
            if (Schema::hasColumn('sales_officer_activities', 'next_follow_up')) {
                Schema::table('sales_officer_activities', function (Blueprint $table) {
                    $table->dropColumn('next_follow_up');
                });
            }
            
            if (Schema::hasColumn('sales_officer_activities', 'follow_up_type')) {
                Schema::table('sales_officer_activities', function (Blueprint $table) {
                    $table->dropColumn('follow_up_type');
                });
            }
        }
    }
};
