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
        Schema::create('company_divisions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contact_id')->constrained('sales_officer_contacts')->onDelete('cascade');
            $table->string('name');
            $table->integer('visit_count')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_divisions');
    }
};
