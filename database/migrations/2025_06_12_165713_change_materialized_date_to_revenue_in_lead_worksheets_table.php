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
        Schema::table('lead_worksheets', function (Blueprint $table) {
            $table->renameColumn('materialized_date', 'materialized_revenue');
        });

        Schema::table('lead_worksheets', function (Blueprint $table) {
            $table->decimal('materialized_revenue', 15, 2)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lead_worksheets', function (Blueprint $table) {
            $table->date('materialized_revenue')->nullable()->change();
        });
        
        Schema::table('lead_worksheets', function (Blueprint $table) {
            $table->renameColumn('materialized_revenue', 'materialized_date');
        });
    }
};
