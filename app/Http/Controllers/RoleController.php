<?php
namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    // List roles
    public function index()
    {
        $roles = Role::with('permissions')->get();
        return view('userroles.index', compact('roles'));
    }

    // Show create role form
    public function create()
    {
        $allPermissions = Permission::all()->groupBy('group_name');
        return view('userroles.create', compact('allPermissions'));
    }

    // Store new role
    public function store(Request $request)
    {
        $request->validate([
            'role_name' => 'required|unique:roles,role_name|max:60',
            'description' => 'nullable|string',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        // Create role
        $role = Role::create([
            'role_name' => $request->role_name,
            'description' => $request->description,
        ]);

        // Assign permissions
        $role->assignPermissions($request->permissions ?? []);

        return redirect()->route('userroles.create')->with('success', 'Role created successfully!');
    }

    // Show edit role form
    public function edit($id)
    {
        $role = Role::with('permissions')->findOrFail($id);
        $allPermissions = Permission::all()->groupBy('group_name');
        $rolePermissions = $role->permissions->pluck('id')->toArray();

        return view('userroles.edit', compact('role', 'allPermissions', 'rolePermissions'));
    }
    public function show($id)
    {
        $role = Role::findOrFail($id);
        return view('userroles.show', compact('role'));
    }

    // Update role
    public function update(Request $request, Role $role)
    {
        $request->validate([
            'role_name'   => 'required|max:60',
            'description' => 'nullable|string',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        // Update role
        $role->update([
            'role_name' => $request->role_name,
            'description' => $request->description,
        ]);

        // Sync permissions (attach/detach)
        $role->permissions()->sync($request->permissions ?? []);

        return redirect()->route('userroles.index')->with('success', 'Role updated successfully!');
    }

    // Delete role
    public function destroy(Role $role)
    {
        $role->removeAllPermissions();
        $role->delete();
        return redirect()->route('userroles.index')->with('success', 'Role deleted successfully!');
    }
}
