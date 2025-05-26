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
            $table->timestamp('viewed_at')->nullable()->after('survey_token');
            // Tambahkan juga kolom status general jika belum ada & diinginkan
            // $table->string('status')->default('pending')->after('survey_token'); 
            // 'pending', 'viewed', 'submitted' / 'completed'
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('feedback_surveys', function (Blueprint $table) {
            $table->dropColumn('viewed_at');
            // $table->dropColumn('status');
        });
    }
};
