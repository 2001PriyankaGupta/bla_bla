<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\AdminUser;
use App\Models\Role;
use App\Models\Permission;
use App\Models\ApiKey;
use App\Models\Tax;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index()
{
    $settings = Setting::first();
    $apiKeys = ApiKey::all();
    $roles = Role::with('permissions')->get();
    $permissions = Permission::all();
    $taxes = Tax::all();
    
    // Remove 'permissions' from with() method
    $admins = AdminUser::where('is_super_admin', true)
        ->with('roles') // Only roles, no permissions
        ->get();

    return view('admin.settings.index', compact(
        'settings', 
        'apiKeys', 
        'roles', 
        'permissions', 
        'taxes', 
        'admins'
    ));
}

    // General Settings
    public function updateGeneral(Request $request)
    {
        $request->validate([
            'app_name' => 'required|string|max:255',
            'contact_email' => 'required|email',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'gst_percentage' => 'required|numeric|min:0|max:100',
            'rounding_rules' => 'required|in:nearest,up,down',
            'surcharge_configuration.peak_hours' => 'required|numeric|min:1',
            'surcharge_configuration.late_night' => 'required|numeric|min:1',
            'surcharge_configuration.weekend' => 'required|numeric|min:1',
        ]);

        DB::transaction(function () use ($request) {
            $settings = Setting::firstOrCreate([]);

            $data = $request->only([
                'app_name', 
                'contact_email', 
                'gst_percentage', 
                'rounding_rules'
            ]);

            // Handle logo upload
            if ($request->hasFile('logo')) {
                // Delete old logo if exists
                if ($settings->logo) {
                    Storage::delete($settings->logo);
                }
                $data['logo'] = $request->file('logo')->store('settings/logo', 'public');
            }

            // Handle surcharge configuration
            if ($request->has('surcharge_configuration')) {
                $data['surcharge_configuration'] = $request->surcharge_configuration;
            }

            $settings->update($data);
        });

        return redirect()->back()->with('success', 'General settings updated successfully.');
    }

    // API Keys Management
    public function storeApiKey(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'key' => 'required|string|unique:api_keys,key',
            'secret' => 'required|string',
        ]);

        ApiKey::create($request->all());

        return redirect()->back()->with('success', 'API key created successfully.');
    }

    public function editApiKey($id)
    {
        $apiKey = ApiKey::findOrFail($id);
        
        return response()->json([
            'id' => $apiKey->id,
            'name' => $apiKey->name,
            'key' => $apiKey->key,
            'secret' => $apiKey->secret,
        ]);
    }

    public function updateApiKey(Request $request, $id)
    {
        $apiKey = ApiKey::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'key' => [
                'required',
                'string',
                Rule::unique('api_keys')->ignore($apiKey->id)
            ],
            'secret' => 'required|string',
        ]);

        $apiKey->update($request->all());

        return redirect()->back()->with('success', 'API key updated successfully.');
    }

    public function deleteApiKey($id)
    {
        $apiKey = ApiKey::findOrFail($id);
        $apiKey->delete();

        return redirect()->back()->with('success', 'API key deleted successfully.');
    }

    public function regenerateApiKey($id)
    {
        $apiKey = ApiKey::findOrFail($id);
        
        $newKey = 'ak_' . bin2hex(random_bytes(16));
        $newSecret = 'as_' . bin2hex(random_bytes(32));
        
        $apiKey->update([
            'key' => $newKey,
            'secret' => $newSecret,
        ]);

        return redirect()->back()->with('success', 'API key regenerated successfully.');
    }

    // Taxes Management
    public function storeTax(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'rate' => 'required|numeric|min:0',
            'type' => 'required|in:percentage,fixed',
            'description' => 'nullable|string|max:500',
            'is_active' => 'boolean',
        ]);

        Tax::create([
            'name' => $request->name,
            'rate' => $request->rate,
            'type' => $request->type,
            'description' => $request->description,
            'is_active' => $request->has('is_active') ? $request->is_active : true,
        ]);

        return redirect()->back()->with('success', 'Tax created successfully.');
    }

    public function editTax($id)
    {
        $tax = Tax::findOrFail($id);
        
        return response()->json([
            'id' => $tax->id,
            'name' => $tax->name,
            'rate' => $tax->rate,
            'type' => $tax->type,
            'description' => $tax->description,
            'is_active' => $tax->is_active,
        ]);
    }

    public function updateTax(Request $request, $id)
    {
        $tax = Tax::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'rate' => 'required|numeric|min:0',
            'type' => 'required|in:percentage,fixed',
            'description' => 'nullable|string|max:500',
            'is_active' => 'boolean',
        ]);

        $tax->update([
            'name' => $request->name,
            'rate' => $request->rate,
            'type' => $request->type,
            'description' => $request->description,
            'is_active' => $request->has('is_active') ? $request->is_active : $tax->is_active,
        ]);

        return redirect()->back()->with('success', 'Tax updated successfully.');
    }

    public function deleteTax($id)
    {
        $tax = Tax::findOrFail($id);
        $tax->delete();

        return redirect()->back()->with('success', 'Tax deleted successfully.');
    }

    // Roles Management
    public function storeRole(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'description' => 'nullable|string|max:500',
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        DB::transaction(function () use ($request) {
            $role = Role::create([
                'name' => $request->name,
                'description' => $request->description,
            ]);

            $role->permissions()->attach($request->permissions);
        });

        return redirect()->back()->with('success', 'Role created successfully.');
    }

    public function editRole($id)
    {
        $role = Role::with('permissions')->findOrFail($id);
        
        return response()->json([
            'id' => $role->id,
            'name' => $role->name,
            'description' => $role->description,
            'permissions' => $role->permissions->pluck('id')->toArray(),
        ]);
    }

    public function updateRole(Request $request, $id)
    {
        $role = Role::findOrFail($id);

        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('roles')->ignore($role->id)
            ],
            'description' => 'nullable|string|max:500',
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        DB::transaction(function () use ($request, $role) {
            $role->update([
                'name' => $request->name,
                'description' => $request->description,
            ]);

            $role->permissions()->sync($request->permissions);
        });

        return redirect()->back()->with('success', 'Role updated successfully.');
    }

    public function deleteRole($id)
    {
        $role = Role::findOrFail($id);
        
        // Prevent deleting default role
        if ($role->is_default) {
            return redirect()->back()->with('error', 'Cannot delete default role.');
        }

        // Check if any users are assigned to this role
        if ($role->users()->count() > 0) {
            return redirect()->back()->with('error', 'Cannot delete role that is assigned to users.');
        }

        $role->delete();

        return redirect()->back()->with('success', 'Role deleted successfully.');
    }

    public function assignAdminRole(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'role_id' => 'required|exists:roles,id',
        ]);

        $user = User::findOrFail($request->user_id);
        $user->roles()->sync([$request->role_id]);

        return redirect()->back()->with('success', 'Role assigned successfully.');
    }

    // Admin Users Management
    public function storeAdminUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'role_id' => 'nullable|exists:roles,id',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,slug'
        ]);

        DB::transaction(function () use ($request) {
            $admin = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'is_admin' => true,
            ]);

            // Assign role if provided
            if ($request->role_id) {
                $admin->roles()->attach($request->role_id);
            }

            // Assign direct permissions if provided
            if ($request->permissions) {
                $permissionIds = Permission::whereIn('slug', $request->permissions)->pluck('id');
                $admin->permissions()->attach($permissionIds);
            }
        });

        return redirect()->back()->with('success', 'Admin user created successfully.');
    }

    public function editAdminUser($id)
    {
        $admin = User::with('roles', 'permissions')->findOrFail($id);
        
        return response()->json([
            'id' => $admin->id,
            'name' => $admin->name,
            'email' => $admin->email,
            'roles' => $admin->roles,
            'permissions' => $admin->permissions->pluck('slug')->toArray()
        ]);
    }

    public function updateAdminUser(Request $request, $id)
    {
        $admin = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore($admin->id)
            ],
            'password' => 'nullable|min:8|confirmed',
            'role_id' => 'nullable|exists:roles,id',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,slug'
        ]);

        DB::transaction(function () use ($request, $admin) {
            $updateData = [
                'name' => $request->name,
                'email' => $request->email,
            ];

            if ($request->password) {
                $updateData['password'] = Hash::make($request->password);
            }

            $admin->update($updateData);

            // Sync role
            if ($request->role_id) {
                $admin->roles()->sync([$request->role_id]);
            } else {
                $admin->roles()->detach();
            }

            // Sync direct permissions
            if ($request->permissions) {
                $permissionIds = Permission::whereIn('slug', $request->permissions)->pluck('id');
                $admin->permissions()->sync($permissionIds);
            } else {
                $admin->permissions()->detach();
            }
        });

        return redirect()->back()->with('success', 'Admin user updated successfully.');
    }

    public function deleteAdminUser($id)
    {
        $admin = User::findOrFail($id);
        
        // Prevent deleting yourself
        if ($admin->id === auth()->id()) {
            return redirect()->back()->with('error', 'You cannot delete your own account.');
        }

        $admin->delete();

        return redirect()->back()->with('success', 'Admin user deleted successfully.');
    }
}