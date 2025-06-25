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
        // Schema::table('bookings', function (Blueprint $table) {
        //     $table->string('booking_type')->default('internal')->after('description');
        //     $table->text('external_description')->nullable()->after('booking_type');
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Schema::table('bookings', function (Blueprint $table) {
        //     $table->dropColumn('booking_type');
        //     $table->dropColumn('external_description');
        // });
    }
};
