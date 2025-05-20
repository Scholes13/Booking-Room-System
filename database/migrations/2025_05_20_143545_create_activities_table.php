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
        if (!Schema::hasTable('activities')) {
            Schema::create('activities', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->foreignId('department_id')->nullable()->constrained('departments');
                $table->string('activity_type');
                $table->text('description')->nullable();
                $table->string('city')->nullable();
                $table->string('province')->nullable();
                $table->dateTime('start_datetime');
                $table->dateTime('end_datetime');
                $table->string('status')->default('scheduled');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
