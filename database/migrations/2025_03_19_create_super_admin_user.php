<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if the super admin user already exists
        $existingUser = User::where('email', 'it@werkudara.com')->first();
        
        if (!$existingUser) {
            // Create the super admin user
            User::create([
                'name' => 'Super Admin',
                'email' => 'it@werkudara.com',
                'password' => Hash::make('Werkudara@2025'),
                'role' => 'superadmin',
            ]);
        } else {
            // Update existing user to superadmin if needed
            if ($existingUser->role !== 'superadmin') {
                $existingUser->role = 'superadmin';
                $existingUser->save();
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Do not delete the user when rolling back
        // This prevents accidental data loss
    }
}; 