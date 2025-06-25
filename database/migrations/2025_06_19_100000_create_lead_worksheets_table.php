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
        Schema::create('lead_worksheets', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('feedback_survey_id')->constrained('feedback_surveys')->onDelete('cascade');
            $table->foreignId('pic_employee_id')->nullable()->constrained('employees')->onDelete('set null');
            
            $table->string('project_name')->nullable();
            $table->json('service_type')->nullable();
            $table->string('line_of_business')->nullable();
            $table->string('current_status')->default('New');
            $table->string('follow_up_status')->nullable();
            
            $table->text('requirements')->nullable();
            $table->decimal('estimated_revenue', 15, 2)->nullable();
            $table->decimal('materialized_revenue', 15, 2)->nullable();

            $table->timestamp('month_receive_lead')->nullable();
            $table->timestamps();

            $table->unique('feedback_survey_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lead_worksheets');
    }
}; 