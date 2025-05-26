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
        Schema::table('sales_officer_activities', function (Blueprint $table) {
            if (!Schema::hasColumn('sales_officer_activities', 'country_id')) {
                if (Schema::hasColumn('sales_officer_activities', 'country')) {
                    $table->unsignedBigInteger('country_id')->nullable()->after('country');
                } else {
                    $table->unsignedBigInteger('country_id')->nullable(); // Add at the end or define a default existing column
                }
            }

            if (!Schema::hasColumn('sales_officer_activities', 'state_id')) {
                if (Schema::hasColumn('sales_officer_activities', 'province')) {
                    $table->unsignedBigInteger('state_id')->nullable()->after('province');
                } elseif (Schema::hasColumn('sales_officer_activities', 'country_id')) {
                    $table->unsignedBigInteger('state_id')->nullable()->after('country_id');
                } else {
                    $table->unsignedBigInteger('state_id')->nullable();
                }
            }

            if (!Schema::hasColumn('sales_officer_activities', 'city_id')) {
                if (Schema::hasColumn('sales_officer_activities', 'city')) {
                    $table->unsignedBigInteger('city_id')->nullable()->after('city');
                } elseif (Schema::hasColumn('sales_officer_activities', 'state_id')) {
                    $table->unsignedBigInteger('city_id')->nullable()->after('state_id');
                } else {
                    $table->unsignedBigInteger('city_id')->nullable();
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales_officer_activities', function (Blueprint $table) {
            $columnsToDrop = [];
            if (Schema::hasColumn('sales_officer_activities', 'country_id')) {
                $columnsToDrop[] = 'country_id';
            }
            if (Schema::hasColumn('sales_officer_activities', 'state_id')) {
                $columnsToDrop[] = 'state_id';
            }
            if (Schema::hasColumn('sales_officer_activities', 'city_id')) {
                $columnsToDrop[] = 'city_id';
            }
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
