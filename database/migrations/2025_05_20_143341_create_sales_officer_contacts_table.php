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
        if (!Schema::hasTable('sales_officer_contacts')) {
            Schema::create('sales_officer_contacts', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->string('company_name');
                $table->string('line_of_business');
                $table->text('company_address')->nullable();
                $table->string('contact_name')->nullable();
                $table->string('position')->nullable();
                $table->string('phone_number')->nullable();
                $table->string('email')->nullable();
                $table->unsignedBigInteger('sales_mission_detail_id')->nullable();
                $table->text('notes')->nullable();
                $table->string('status')->default('active');
                $table->integer('visit_count')->default(0);
                $table->string('country')->nullable();
                $table->string('province')->nullable();
                $table->string('city')->nullable();
                // Business Description Fields
                $table->text('general_information')->nullable();
                $table->text('current_event')->nullable();
                $table->text('target_business')->nullable();
                $table->text('project_type')->nullable();
                $table->text('project_estimation')->nullable();
                $table->string('potential_revenue')->nullable();
                $table->integer('potential_project_count')->nullable();
                $table->timestamps();
                
                // Add foreign keys only if reference tables exist
                if (Schema::hasTable('users')) {
                    $table->foreign('user_id')->references('id')->on('users');
                }
                
                if (Schema::hasTable('sales_mission_details')) {
                    $table->foreign('sales_mission_detail_id')->references('id')->on('sales_mission_details');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_officer_contacts');
    }
};
