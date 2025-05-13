<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class ActivityLogService
{
    /**
     * Log an activity by an admin user
     *
     * @param string $action Type of action (create, update, delete, export)
     * @param string $module The module/entity affected (meeting_rooms, bookings, etc.)
     * @param string $description Description of the activity
     * @param array|null $properties Additional data about the activity
     * @param \Illuminate\Http\Request|null $request The request object (if available)
     * @return \App\Models\ActivityLog
     */
    public static function log($action, $module, $description, $properties = null, Request $request = null)
    {
        if (!$request) {
            $request = request();
        }
        
        // Only log activities for admin users (both admin and admin_bas roles)
        if (!Auth::check() || !in_array(Auth::user()->role, ['admin', 'admin_bas', 'superadmin'])) {
            return null;
        }
        
        $data = [
            'user_id' => Auth::id(),
            'action' => $action,
            'module' => $module,
            'description' => $description,
            'properties' => $properties,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent()
        ];
        
        return ActivityLog::create($data);
    }
    
    /**
     * Helper method for logging create actions
     */
    public static function logCreate($module, $description, $properties = null, Request $request = null)
    {
        return self::log('create', $module, $description, $properties, $request);
    }
    
    /**
     * Helper method for logging update actions
     */
    public static function logUpdate($module, $description, $properties = null, Request $request = null)
    {
        return self::log('update', $module, $description, $properties, $request);
    }
    
    /**
     * Helper method for logging delete actions
     */
    public static function logDelete($module, $description, $properties = null, Request $request = null)
    {
        return self::log('delete', $module, $description, $properties, $request);
    }
    
    /**
     * Helper method for logging export actions
     */
    public static function logExport($module, $description, $properties = null, Request $request = null)
    {
        return self::log('export', $module, $description, $properties, $request);
    }
} 