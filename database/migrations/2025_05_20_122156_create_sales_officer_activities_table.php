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
        Schema::create('sales_officer_activities', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('department_id')->nullable()->constrained('departments')->onDelete('set null');
            $table->text('description');
            $table->string('activity_type');
            $table->string('city');
            $table->string('province');
            $table->dateTime('start_datetime');
            $table->dateTime('end_datetime');
            $table->string('status')->default('scheduled');
            $table->string('result')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('contact_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_officer_activities');
    }
};
