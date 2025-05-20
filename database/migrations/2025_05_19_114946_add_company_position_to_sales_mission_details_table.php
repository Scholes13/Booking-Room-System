<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCompanyPositionToSalesMissionDetailsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('sales_mission_details', function (Blueprint $table) {
            $table->string('company_position')->nullable()->after('company_pic');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales_mission_details', function (Blueprint $table) {
            $table->dropColumn('company_position');
        });
    }
}
