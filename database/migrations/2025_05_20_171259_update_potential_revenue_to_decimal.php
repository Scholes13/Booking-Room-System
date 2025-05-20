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
        // First, backup existing values
        $contacts = DB::table('sales_officer_contacts')
            ->select('id', 'potential_revenue')
            ->get();
            
        // Then, modify the column
        Schema::table('sales_officer_contacts', function (Blueprint $table) {
            $table->decimal('potential_revenue', 18, 2)->nullable()->change();
        });
        
        // Finally, update values with cleaned numeric format
        foreach ($contacts as $contact) {
            if ($contact->potential_revenue) {
                $cleaned = preg_replace('/[^0-9,.]/', '', $contact->potential_revenue);
                $cleaned = str_replace('.', '', $cleaned); // Remove dots (thousand separator)
                $cleaned = str_replace(',', '.', $cleaned); // Replace comma with period for decimal
                
                DB::table('sales_officer_contacts')
                    ->where('id', $contact->id)
                    ->update(['potential_revenue' => $cleaned]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales_officer_contacts', function (Blueprint $table) {
            $table->string('potential_revenue')->nullable()->change();
        });
    }
};
