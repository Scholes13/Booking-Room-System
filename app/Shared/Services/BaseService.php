<?php

namespace App\Shared\Services;

use App\Services\ActivityLogService;
use Illuminate\Support\Facades\Auth;

abstract class BaseService
{
    /**
     * Log activity for current user
     *
     * @param string $action
     * @param string $module
     * @param string $description
     * @param array|null $properties
     * @return mixed
     */
    protected function logActivity($action, $module, $description, $properties = null)
    {
        return ActivityLogService::log($action, $module, $description, $properties);
    }
    
    /**
     * Check if current user has permission for specific role
     *
     * @param array $allowedRoles
     * @return bool
     */
    protected function hasPermission(array $allowedRoles): bool
    {
        if (!Auth::check()) {
            return false;
        }
        
        return in_array(Auth::user()->role, $allowedRoles);
    }
    
    /**
     * Get current authenticated user
     *
     * @return \App\Models\User|null
     */
    protected function getCurrentUser()
    {
        return Auth::user();
    }
    
    /**
     * Validate required permissions before executing action
     *
     * @param array $allowedRoles
     * @throws \Exception
     */
    protected function validatePermission(array $allowedRoles)
    {
        if (!$this->hasPermission($allowedRoles)) {
            throw new \Exception('Unauthorized access. Required roles: ' . implode(', ', $allowedRoles));
        }
    }
}