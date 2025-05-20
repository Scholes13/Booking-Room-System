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
        Schema::create('sales_officer_contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('company_name');
            $table->string('company_address')->nullable();
            $table->string('contact_name');
            $table->string('position')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('email')->nullable();
            $table->foreignId('sales_mission_detail_id')->nullable()->constrained('sales_mission_details')->onDelete('set null');
            $table->text('notes')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_officer_contacts');
    }
};
