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
        Schema::table('sales_officer_contacts', function (Blueprint $table) {
            $table->string('line_of_business')->nullable()->after('company_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales_officer_contacts', function (Blueprint $table) {
            $table->dropColumn('line_of_business');
        });
    }
}; 