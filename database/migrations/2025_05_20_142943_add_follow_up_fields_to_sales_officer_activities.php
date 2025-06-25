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
            Schema::table('sales_officer_activities', function (Blueprint $table) {
                // Add 'next_follow_up' column
                if (!Schema::hasColumn('sales_officer_activities', 'next_follow_up')) {
                    if (Schema::hasColumn('sales_officer_activities', 'products_discussed')) {
                        $table->dateTime('next_follow_up')->nullable()->after('products_discussed');
                    } else {
                        // If products_discussed doesn't exist, try to place it after another common column or add it at the end
                        $existingColumnForAfter = Schema::hasColumn('sales_officer_activities', 'status') ? 'status' : (Schema::hasColumn('sales_officer_activities', 'description') ? 'description' : null);
                        if ($existingColumnForAfter) {
                             $table->dateTime('next_follow_up')->nullable()->after($existingColumnForAfter);
                        } else {
                             $table->dateTime('next_follow_up')->nullable();
                        }
                    }
                }
                
                // Add 'follow_up_type' column
                if (!Schema::hasColumn('sales_officer_activities', 'follow_up_type')) {
                    if (Schema::hasColumn('sales_officer_activities', 'next_follow_up')) {
                        $table->string('follow_up_type')->nullable()->after('next_follow_up');
                    } else {
                        // If next_follow_up doesn't exist (should have been added above if missing, but as a fallback)
                        // Try to place it after another common column or add it at the end
                        $existingColumnForAfter = Schema::hasColumn('sales_officer_activities', 'products_discussed') ? 'products_discussed' : (Schema::hasColumn('sales_officer_activities', 'status') ? 'status' : (Schema::hasColumn('sales_officer_activities', 'description') ? 'description' : null));
                        if ($existingColumnForAfter){
                            $table->string('follow_up_type')->nullable()->after($existingColumnForAfter);
                        } else {
                            $table->string('follow_up_type')->nullable();
                        }
                    }
                }
            });
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
