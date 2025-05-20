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
        if (!Schema::hasTable('contact_people')) {
            Schema::create('contact_people', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('contact_id');
                $table->unsignedBigInteger('division_id')->nullable();
                $table->string('title')->default('Mr'); // Mr, Mrs, Ms
                $table->string('name');
                $table->string('position')->nullable();
                $table->string('phone_number')->nullable();
                $table->string('email')->nullable();
                $table->boolean('is_primary')->default(false);
                $table->timestamps();
                
                // Add foreign keys only if reference tables exist
                if (Schema::hasTable('sales_officer_contacts')) {
                    $table->foreign('contact_id')->references('id')->on('sales_officer_contacts')->onDelete('cascade');
                }
                
                if (Schema::hasTable('company_divisions')) {
                    $table->foreign('division_id')->references('id')->on('company_divisions')->onDelete('cascade');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contact_people');
    }
};
