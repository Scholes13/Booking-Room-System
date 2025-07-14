<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::orderBy('name', 'asc')->paginate(10);
        return view('superadmin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('superadmin.users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'role'     => 'required|in:admin,admin_bas,superadmin,sales_mission,sales_officer,lead',
        ]);

        User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => $validated['password'], // Model should have a mutator for hashing
            'role'     => $validated['role'],
        ]);

        return redirect()->route('superadmin.users.index')
                         ->with('success', 'User baru berhasil dibuat!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        return view('superadmin.users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role'  => 'required|in:admin,admin_bas,superadmin,sales_mission,sales_officer,lead',
            'password' => 'nullable|min:6',
        ]);

        $userData = [
            'name'  => $validated['name'],
            'email' => $validated['email'],
            'role'  => $validated['role'],
        ];

        if (!empty($validated['password'])) {
            $userData['password'] = $validated['password'];
        }

        $user->update($userData);

        return redirect()->route('superadmin.users.index')
                         ->with('success', 'User berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        // Optional: Prevent superadmin from deleting themselves
        if ($user->id === auth()->id()) {
            return redirect()->route('superadmin.users.index')
                             ->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }
        
        $user->delete();

        return redirect()->route('superadmin.users.index')
                         ->with('success', 'User berhasil dihapus!');
    }
} 