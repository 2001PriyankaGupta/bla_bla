<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_default'
    ];

    protected $casts = [
        'is_default' => 'boolean'
    ];

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permission');
    }

    public function adminUsers()
    {
        return $this->belongsToMany(AdminUser::class, 'admin_user_role');
    }

    public function assignPermissions(array $permissionIds)
    {
        return $this->permissions()->sync($permissionIds);
    }



    
}