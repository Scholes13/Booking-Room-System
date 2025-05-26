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
        // Check if these columns don't exist yet
        if (!Schema::hasColumn('feedback_surveys', 'visited_time')) {
            Schema::table('feedback_surveys', function (Blueprint $table) {
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
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('feedback_surveys', function (Blueprint $table) {
            $table->dropColumn([
                'visited_time',
                'contact_salutation',
                'contact_name',
                'contact_job_title',
                'department',
                'contact_mobile',
                'contact_email',
                'decision_maker_status',
                'sales_call_outcome',
                'next_follow_up',
                'next_follow_up_other',
                'product_interested',
                'status_lead',
                'potential_revenue',
                'key_discussion_points',
                'has_documentation',
                'has_business_card'
            ]);
        });
    }
}; 