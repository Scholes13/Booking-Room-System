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
        if (!Schema::hasTable('sales_officer_activities')) {
            Schema::create('sales_officer_activities', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('department_id')->nullable();
                $table->text('description')->nullable();
                $table->string('activity_type');
                $table->string('meeting_type'); // Online, Offline
                $table->string('city')->nullable();
                $table->string('province')->nullable();
                $table->string('country')->nullable();
                $table->dateTime('start_datetime');
                $table->dateTime('end_datetime');
                $table->integer('month_number');
                $table->integer('week_number');
                $table->string('status')->default('scheduled'); // scheduled, ongoing, completed, pending_report
                $table->text('result')->nullable();
                $table->text('notes')->nullable();
                $table->unsignedBigInteger('contact_id');
                $table->unsignedBigInteger('division_id')->nullable();
                $table->unsignedBigInteger('pic_id')->nullable();
                $table->string('account_status')->default('New'); // New, Contracted, Existing
                $table->integer('products_discussed')->default(1);
                $table->dateTime('next_follow_up')->nullable();
                $table->string('follow_up_type')->nullable();
                $table->timestamps();
                
                // Add foreign keys only if reference tables exist
                if (Schema::hasTable('users')) {
                    $table->foreign('user_id')->references('id')->on('users');
                }
                
                if (Schema::hasTable('departments')) {
                    $table->foreign('department_id')->references('id')->on('departments');
                }
                
                if (Schema::hasTable('sales_officer_contacts')) {
                    $table->foreign('contact_id')->references('id')->on('sales_officer_contacts');
                }
                
                if (Schema::hasTable('company_divisions')) {
                    $table->foreign('division_id')->references('id')->on('company_divisions');
                }
                
                if (Schema::hasTable('contact_people')) {
                    $table->foreign('pic_id')->references('id')->on('contact_people');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_officer_activities');
    }
};
