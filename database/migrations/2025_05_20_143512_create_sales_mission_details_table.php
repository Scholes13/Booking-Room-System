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
        if (!Schema::hasTable('sales_mission_details')) {
            Schema::create('sales_mission_details', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('activity_id');
                $table->string('company_name');
                $table->string('company_pic')->nullable();
                $table->string('company_position')->nullable();
                $table->string('company_contact')->nullable();
                $table->string('company_email')->nullable();
                $table->text('company_address')->nullable();
                $table->string('potential_business')->nullable();
                $table->timestamps();
                
                // Add foreign key only if activities table exists
                if (Schema::hasTable('activities')) {
                    $table->foreign('activity_id')->references('id')->on('activities')->onDelete('cascade');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_mission_details');
    }
};
