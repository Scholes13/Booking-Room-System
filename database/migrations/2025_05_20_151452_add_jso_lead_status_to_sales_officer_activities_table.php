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
            if (!Schema::hasColumn('sales_officer_activities', 'jso_lead_status')) {
                Schema::table('sales_officer_activities', function (Blueprint $table) {
                    $table->string('jso_lead_status')->nullable()->after('follow_up_type')
                        ->comment('Lead status: Closed/Cold, Closed/Handed Over, Closed/No Prospect, Cold Lead, Handed Over, Hot Lead, Lost Lead, On progress, Open/Cold Lead, Open/Hot Lead');
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
            if (Schema::hasColumn('sales_officer_activities', 'jso_lead_status')) {
                Schema::table('sales_officer_activities', function (Blueprint $table) {
                    $table->dropColumn('jso_lead_status');
                });
            }
        }
    }
};
