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
        Schema::create('contact_people', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contact_id')->constrained('sales_officer_contacts')->onDelete('cascade');
            $table->foreignId('division_id')->nullable()->constrained('company_divisions')->onDelete('set null');
            $table->enum('title', ['Mr', 'Mrs', 'Ms']);
            $table->string('name');
            $table->string('position')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('email')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_primary')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contact_people');
    }
};
