<?php

namespace App\Shared\Enums;

enum UserRole: string
{
    case ADMIN = 'admin';
    case SUPERADMIN = 'superadmin';
    case ADMIN_BAS = 'admin_bas';
    case SALES_MISSION = 'sales_mission';
    case SALES_OFFICER = 'sales_officer';
    case LEAD = 'lead';
    
    /**
     * Get human readable label for the role
     *
     * @return string
     */
    public function getLabel(): string
    {
        return match($this) {
            self::ADMIN => 'Administrator',
            self::SUPERADMIN => 'Super Administrator',
            self::ADMIN_BAS => 'Admin BAS',
            self::SALES_MISSION => 'Sales Mission',
            self::SALES_OFFICER => 'Sales Officer',
            self::LEAD => 'Lead',
        };
    }
    
    /**
     * Get dashboard route for the role
     *
     * @return string
     */
    public function getDashboardRoute(): string
    {
        return match($this) {
            self::ADMIN => 'admin.dashboard',
            self::SUPERADMIN => 'superadmin.dashboard',
            self::ADMIN_BAS => 'bas.dashboard',
            self::SALES_MISSION => 'sales_mission.dashboard',
            self::SALES_OFFICER => 'sales_officer.dashboard',
            self::LEAD => 'lead.dashboard',
        };
    }
    
    /**
     * Get all available roles as array
     *
     * @return array
     */
    public static function getAllRoles(): array
    {
        return array_column(self::cases(), 'value');
    }
    
    /**
     * Check if role has admin privileges
     *
     * @return bool
     */
    public function hasAdminPrivileges(): bool
    {
        return in_array($this, [
            self::ADMIN,
            self::SUPERADMIN,
            self::ADMIN_BAS
        ]);
    }
    
    /**
     * Check if role has sales privileges
     *
     * @return bool
     */
    public function hasSalesPrivileges(): bool
    {
        return in_array($this, [
            self::SALES_MISSION,
            self::SALES_OFFICER,
            self::SUPERADMIN
        ]);
    }
    
    /**
     * Get roles that can access booking management
     *
     * @return array
     */
    public static function getBookingManagerRoles(): array
    {
        return [
            self::ADMIN->value,
            self::SUPERADMIN->value,
            self::ADMIN_BAS->value
        ];
    }
    
    /**
     * Get roles that can access activity management
     *
     * @return array
     */
    public static function getActivityManagerRoles(): array
    {
        return [
            self::ADMIN->value,
            self::SUPERADMIN->value,
            self::ADMIN_BAS->value,
            self::SALES_MISSION->value,
            self::SALES_OFFICER->value
        ];
    }
}