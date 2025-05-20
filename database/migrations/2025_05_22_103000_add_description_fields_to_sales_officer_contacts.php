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
            $table->text('general_information')->nullable()->after('notes');
            $table->text('current_event')->nullable()->after('general_information');
            $table->text('target_business')->nullable()->after('current_event');
            $table->text('project_type')->nullable()->after('target_business');
            $table->text('project_estimation')->nullable()->after('project_type');
            $table->string('potential_revenue')->nullable()->after('project_estimation');
            $table->integer('potential_project_count')->nullable()->after('potential_revenue');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales_officer_contacts', function (Blueprint $table) {
            $table->dropColumn([
                'general_information',
                'current_event',
                'target_business',
                'project_type',
                'project_estimation',
                'potential_revenue',
                'potential_project_count'
            ]);
        });
    }
}; 