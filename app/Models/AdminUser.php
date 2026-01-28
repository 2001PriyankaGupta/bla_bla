<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class AdminUser extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'permissions',
        'is_super_admin'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'permissions' => 'array',
        'is_super_admin' => 'boolean'
    ];

    // Keep only the roles relationship
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'admin_user_role');
    }

    // Remove the permissions() relationship method entirely
    // public function permissions()
    // {
    //     return $this->belongsToMany(Permission::class, 'admin_user_permission');
    // }

    // Check if user has a specific permission
    public function hasPermission($permission)
    {
        // Super admin has all permissions
        if ($this->is_super_admin) {
            return true;
        }

        // Check direct permissions from JSON column
        $userPermissions = $this->permissions ?? [];
        if (in_array($permission, $userPermissions)) {
            return true;
        }

        // Check permissions through roles
        foreach ($this->roles as $role) {
            if ($role->permissions->contains('slug', $permission)) {
                return true;
            }
        }

        return false;
    }

    // Check if user has any of the given permissions
    public function hasAnyPermission(array $permissions)
    {
        // Super admin has all permissions
        if ($this->is_super_admin) {
            return true;
        }

        // Check direct permissions from JSON column
        $userPermissions = $this->permissions ?? [];
        if (count(array_intersect($permissions, $userPermissions)) > 0) {
            return true;
        }

        // Check permissions through roles
        foreach ($this->roles as $role) {
            if ($role->permissions->whereIn('slug', $permissions)->count() > 0) {
                return true;
            }
        }

        return false;
    }

    // Assign permissions to user (stores in JSON column)
    public function assignPermissions(array $permissions)
    {
        $this->permissions = $permissions;
        return $this->save();
    }

    // Assign roles to user
    public function assignRoles(array $roleIds)
    {
        return $this->roles()->sync($roleIds);
    }
}