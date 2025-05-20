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
        if (Schema::hasTable('sales_officer_activities')) {
            if (!Schema::hasColumn('sales_officer_activities', 'follow_up_frequency')) {
                Schema::table('sales_officer_activities', function (Blueprint $table) {
                    $table->string('follow_up_frequency')->nullable()->after('follow_up_type')
                        ->comment('Frequency options: Weekly, Monthly, Bi-Weekly, Quarterly, Semester, Yearly, As Requested');
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
            if (Schema::hasColumn('sales_officer_activities', 'follow_up_frequency')) {
                Schema::table('sales_officer_activities', function (Blueprint $table) {
                    $table->dropColumn('follow_up_frequency');
                });
            }
        }
    }
};
