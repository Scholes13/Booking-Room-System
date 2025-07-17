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
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name')->index();
            $table->text('address')->nullable();
            $table->string('city', 100)->nullable()->index();
            $table->string('province', 100)->nullable();
            $table->string('industry', 100)->nullable();
            $table->enum('company_size', ['startup', 'small', 'medium', 'large', 'enterprise'])->nullable();
            $table->enum('status', ['prospect', 'active', 'inactive', 'blacklist'])->default('prospect')->index();
            $table->string('website')->nullable();
            $table->text('description')->nullable();
            $table->json('metadata')->nullable(); // For additional flexible data
            $table->timestamps();
            
            // Indexes for better performance
            $table->index(['name', 'city']);
            $table->index(['status', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};