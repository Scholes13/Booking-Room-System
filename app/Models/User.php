<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Role constants
     */
    const ROLE_ADMIN = 'admin';
    const ROLE_SUPERADMIN = 'superadmin';
    const ROLE_ADMIN_BAS = 'admin_bas';
    const ROLE_SALES_MISSION = 'sales_mission';
    const ROLE_SALES_OFFICER = 'sales_officer';
    const ROLE_LEAD = 'lead';

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

    /**
     * Check if user is Admin
     */
    public function isAdmin()
    {
        return $this->role === self::ROLE_ADMIN;
    }

    /**
     * Check if user is SuperAdmin
     */
    public function isSuperAdmin()
    {
        return $this->role === self::ROLE_SUPERADMIN;
    }

    /**
     * Check if user is Admin BAS
     */
    public function isAdminBAS()
    {
        return $this->role === self::ROLE_ADMIN_BAS;
    }

    /**
     * Check if user is Sales Mission
     */
    public function isSalesMission()
    {
        return $this->role === self::ROLE_SALES_MISSION;
    }

    /**
     * Check if user is Sales Officer
     */
    public function isSalesOfficer()
    {
        return $this->role === self::ROLE_SALES_OFFICER;
    }

    /**
     * Check if user is Lead
     */
    public function isLead()
    {
        return $this->role === self::ROLE_LEAD;
    }
    
    /**
     * Get the sales officer activities owned by this user.
     */
    public function salesOfficerActivities()
    {
        return $this->hasMany(SalesOfficerActivity::class);
    }
    
    /**
     * Get the sales officer contacts owned by this user.
     */
    public function salesOfficerContacts()
    {
        return $this->hasMany(SalesOfficerContact::class);
    }
}
