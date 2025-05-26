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
        Schema::table('feedback_surveys', function (Blueprint $table) {
            $table->string('blitz_team_name')->nullable()->after('survey_token');
            $table->string('blitz_company_name')->nullable()->after('blitz_team_name');
            $table->dateTime('blitz_visit_start_datetime')->nullable()->after('blitz_company_name');
            $table->dateTime('blitz_visit_end_datetime')->nullable()->after('blitz_visit_start_datetime');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('feedback_surveys', function (Blueprint $table) {
            $table->dropColumn([
                'blitz_team_name',
                'blitz_company_name',
                'blitz_visit_start_datetime',
                'blitz_visit_end_datetime'
            ]);
        });
    }
};
