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
        if (!Schema::hasTable('company_divisions')) {
            Schema::create('company_divisions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('contact_id');
                $table->string('name');
                $table->string('description')->nullable();
                $table->integer('visit_count')->default(0);
                $table->timestamps();
                
                // Add foreign key only if reference table exists
                if (Schema::hasTable('sales_officer_contacts')) {
                    $table->foreign('contact_id')->references('id')->on('sales_officer_contacts')->onDelete('cascade');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_divisions');
    }
};
