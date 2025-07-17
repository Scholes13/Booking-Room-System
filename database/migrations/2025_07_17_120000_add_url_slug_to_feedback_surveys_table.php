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
            $table->string('url_slug')->nullable()->unique()->after('survey_token');
            $table->index('url_slug'); // For faster lookups
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('feedback_surveys', function (Blueprint $table) {
            $table->dropIndex(['url_slug']);
            $table->dropColumn('url_slug');
        });
    }
};