<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Field yang boleh diisi secara mass assignment.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * Field yang disembunyikan saat serialisasi (misalnya JSON).
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Casting attribute. 'password' => 'hashed' akan otomatis melakukan hashing.
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed', // Laravel akan otomatis Hash::make() saat menyimpan
    ];
}
