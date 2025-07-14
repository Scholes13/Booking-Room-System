<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasColumn('sales_officer_contacts', 'potential_revenue')) {
            // First, backup existing values
            $contacts = DB::table('sales_officer_contacts')
                ->select('id', 'potential_revenue')
                ->whereNotNull('potential_revenue') // Only fetch rows where it might need cleaning
                ->get();
                
            // Then, modify the column
            Schema::table('sales_officer_contacts', function (Blueprint $table) {
                $table->decimal('potential_revenue', 18, 2)->nullable()->change();
            });
            
            // Finally, update values with cleaned numeric format
            if ($contacts->isNotEmpty()) {
                foreach ($contacts as $contact) {
                    // The check $contact->potential_revenue is already implicitly handled by whereNotNull
                    $cleaned = preg_replace('/[^0-9,.]/', '', (string) $contact->potential_revenue);
                    $cleaned = str_replace('.', '', $cleaned); // Remove dots (thousand separator)
                    $cleaned = str_replace(',', '.', $cleaned); // Replace comma with period for decimal
                    
                    // Ensure it's a valid number before updating
                    if (is_numeric($cleaned)) {
                        DB::table('sales_officer_contacts')
                            ->where('id', $contact->id)
                            ->update(['potential_revenue' => $cleaned]);
                    } else {
                        // Optionally log an error or set to null if cleaning failed
                        DB::table('sales_officer_contacts')
                            ->where('id', $contact->id)
                            ->update(['potential_revenue' => null]);
                    }
                }
            }
        } else {
            // If the column doesn't exist, this migration might not be necessary for this column
            // or it implies the column should be created first by another migration.
            // For now, we'll do nothing if the column is not present.
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('sales_officer_contacts', 'potential_revenue')) {
            Schema::table('sales_officer_contacts', function (Blueprint $table) {
                // It's safer to ensure the column type is indeed decimal before changing it back to string
                // However, for simplicity in a down migration, we assume it was decimal.
                $table->string('potential_revenue')->nullable()->change();
            });
        }
    }
};
