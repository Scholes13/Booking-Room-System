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
        Schema::create('feedback_surveys', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_assignment_id')->constrained('team_assignments')->onDelete('cascade');
            $table->string('survey_token')->unique(); // Unique token for accessing the survey
            $table->boolean('is_completed')->default(false);
            $table->integer('satisfaction_rating')->nullable(); // 1-5 rating
            $table->text('feedback_comments')->nullable();
            $table->integer('team_rating')->nullable(); // 1-5 rating
            $table->text('improvement_suggestions')->nullable();
            
            // Sales visit report fields
            $table->dateTime('visited_time')->nullable();
            $table->string('contact_salutation')->nullable();
            $table->string('contact_name')->nullable();
            $table->string('contact_job_title')->nullable();
            $table->string('department')->nullable();
            $table->string('contact_mobile')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('decision_maker_status')->nullable();
            $table->string('sales_call_outcome')->nullable();
            $table->string('next_follow_up')->nullable();
            $table->string('next_follow_up_other')->nullable();
            $table->string('product_interested')->nullable();
            $table->string('status_lead')->nullable();
            $table->string('potential_revenue')->nullable();
            $table->text('key_discussion_points')->nullable();
            $table->boolean('has_documentation')->default(false);
            $table->boolean('has_business_card')->default(false);
            
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feedback_surveys');
    }
}; 