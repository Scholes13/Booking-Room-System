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
            if (!Schema::hasColumn('feedback_surveys', 'viewed_at')) {
                $table->timestamp('viewed_at')->nullable()->after('completed_at');
            }
            
            if (!Schema::hasColumn('feedback_surveys', 'view_count')) {
                // Determine column to place after
                $afterViewedAt = Schema::hasColumn('feedback_surveys', 'viewed_at') ? 'viewed_at' : 'completed_at';
                $table->integer('view_count')->default(0)->after($afterViewedAt);
            }

            if (!Schema::hasColumn('feedback_surveys', 'last_viewed_at')) {
                // Determine column to place after
                $afterViewCount = Schema::hasColumn('feedback_surveys', 'view_count') ? 'view_count' : (Schema::hasColumn('feedback_surveys', 'viewed_at') ? 'viewed_at' : 'completed_at');
                $table->timestamp('last_viewed_at')->nullable()->after($afterViewCount);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('feedback_surveys', function (Blueprint $table) {
            $columnsToDrop = [];
            if (Schema::hasColumn('feedback_surveys', 'viewed_at')) {
                $columnsToDrop[] = 'viewed_at';
            }
            if (Schema::hasColumn('feedback_surveys', 'view_count')) {
                $columnsToDrop[] = 'view_count';
            }
            if (Schema::hasColumn('feedback_surveys', 'last_viewed_at')) {
                $columnsToDrop[] = 'last_viewed_at';
            }
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
