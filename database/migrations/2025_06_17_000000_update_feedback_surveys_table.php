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
        Schema::table('feedback_surveys', function (Blueprint $table) {
            $lastAddedColumn = 'is_completed';

            if (!Schema::hasColumn('feedback_surveys', 'visited_time')) {
                $table->dateTime('visited_time')->nullable()->after($lastAddedColumn);
            }
            $lastAddedColumn = 'visited_time';

            if (!Schema::hasColumn('feedback_surveys', 'contact_salutation')) {
                $table->string('contact_salutation')->nullable()->after($lastAddedColumn);
            }
            $lastAddedColumn = 'contact_salutation';

            if (!Schema::hasColumn('feedback_surveys', 'contact_name')) {
                $table->string('contact_name')->nullable()->after($lastAddedColumn);
            }
            $lastAddedColumn = 'contact_name';

            if (!Schema::hasColumn('feedback_surveys', 'contact_job_title')) {
                $table->string('contact_job_title')->nullable()->after($lastAddedColumn);
            }
            $lastAddedColumn = 'contact_job_title';

            if (!Schema::hasColumn('feedback_surveys', 'department')) {
                $table->string('department')->nullable()->after($lastAddedColumn);
            }
            $lastAddedColumn = 'department';

            if (!Schema::hasColumn('feedback_surveys', 'contact_mobile')) {
                $table->string('contact_mobile')->nullable()->after($lastAddedColumn);
            }
            $lastAddedColumn = 'contact_mobile';

            if (!Schema::hasColumn('feedback_surveys', 'contact_email')) {
                $table->string('contact_email')->nullable()->after($lastAddedColumn);
            }
            $lastAddedColumn = 'contact_email';

            if (!Schema::hasColumn('feedback_surveys', 'decision_maker_status')) {
                $table->string('decision_maker_status')->nullable()->after($lastAddedColumn);
            }
            $lastAddedColumn = 'decision_maker_status';

            if (!Schema::hasColumn('feedback_surveys', 'sales_call_outcome')) {
                $table->string('sales_call_outcome')->nullable()->after($lastAddedColumn);
            }
            $lastAddedColumn = 'sales_call_outcome';

            if (!Schema::hasColumn('feedback_surveys', 'next_follow_up')) {
                $table->string('next_follow_up')->nullable()->after($lastAddedColumn);
            }
            $lastAddedColumn = 'next_follow_up';

            if (!Schema::hasColumn('feedback_surveys', 'next_follow_up_other')) {
                $table->string('next_follow_up_other')->nullable()->after($lastAddedColumn);
            }
            $lastAddedColumn = 'next_follow_up_other';

            if (!Schema::hasColumn('feedback_surveys', 'product_interested')) {
                $table->string('product_interested')->nullable()->after($lastAddedColumn);
            }
            $lastAddedColumn = 'product_interested';

            if (!Schema::hasColumn('feedback_surveys', 'status_lead')) {
                $table->string('status_lead')->nullable()->after($lastAddedColumn);
            }
            $lastAddedColumn = 'status_lead';

            if (!Schema::hasColumn('feedback_surveys', 'potential_revenue')) {
                $table->string('potential_revenue')->nullable()->after($lastAddedColumn);
            }
            $lastAddedColumn = 'potential_revenue';

            if (!Schema::hasColumn('feedback_surveys', 'key_discussion_points')) {
                $table->text('key_discussion_points')->nullable()->after($lastAddedColumn);
            }
            $lastAddedColumn = 'key_discussion_points';

            if (!Schema::hasColumn('feedback_surveys', 'has_documentation')) {
                $table->boolean('has_documentation')->default(false)->after($lastAddedColumn);
            }
            $lastAddedColumn = 'has_documentation';

            if (!Schema::hasColumn('feedback_surveys', 'has_business_card')) {
                $table->boolean('has_business_card')->default(false)->after($lastAddedColumn);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('feedback_surveys', function (Blueprint $table) {
            $columnsToDrop = [
                'visited_time', 'contact_salutation', 'contact_name', 'contact_job_title',
                'department', 'contact_mobile', 'contact_email', 'decision_maker_status',
                'sales_call_outcome', 'next_follow_up', 'next_follow_up_other',
                'product_interested', 'status_lead', 'potential_revenue',
                'key_discussion_points', 'has_documentation', 'has_business_card'
            ];
            $existingColumnsToDrop = [];
            foreach ($columnsToDrop as $column) {
                if (Schema::hasColumn('feedback_surveys', $column)) {
                    $existingColumnsToDrop[] = $column;
                }
            }
            if (!empty($existingColumnsToDrop)) {
                $table->dropColumn($existingColumnsToDrop);
            }
        });
    }
}; 