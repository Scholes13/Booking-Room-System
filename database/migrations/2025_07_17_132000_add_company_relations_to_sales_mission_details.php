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
        Schema::table('sales_mission_details', function (Blueprint $table) {
            // Add foreign key columns
            $table->foreignId('company_id')->nullable()->after('activity_id')->constrained()->onDelete('cascade');
            $table->foreignId('company_contact_id')->nullable()->after('company_id')->constrained()->onDelete('set null');
            
            // Add visit type to distinguish initial vs follow-up
            $table->enum('visit_type', ['initial', 'follow_up'])->default('initial')->after('company_contact_id');
            
            // Add visit sequence number for the same company
            $table->integer('visit_sequence')->default(1)->after('visit_type');
            
            // Add indexes for better performance
            $table->index(['company_id', 'visit_sequence']);
            $table->index(['company_id', 'visit_type']);
            $table->index(['visit_type', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales_mission_details', function (Blueprint $table) {
            // Drop foreign key constraints first
            $table->dropForeign(['company_contact_id']);
            $table->dropForeign(['company_id']);
            
            // Drop indexes
            $table->dropIndex(['company_id', 'visit_sequence']);
            $table->dropIndex(['company_id', 'visit_type']);
            $table->dropIndex(['visit_type', 'created_at']);
            
            // Drop columns
            $table->dropColumn(['company_id', 'company_contact_id', 'visit_type', 'visit_sequence']);
        });
    }
};