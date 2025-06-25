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
        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 50)->default('admin')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Untuk rollback, kita bisa kembalikan ke state sebelumnya
            // Namun, ini tergantung pada definisi awal yang kita asumsikan
            // Untuk amannya, kita bisa set ke panjang default lama jika perlu
            $table->string('role')->default('admin')->change();
        });
    }
};
