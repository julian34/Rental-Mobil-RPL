<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function getAllRoles()
    {
        $roles = Role::with('permissions')->get();
        return response()->json(['roles' => $roles], 200);
    }

    public function getRoleById(Role $role)
    {
        return response()->json([
            'role' => $role->load('users', 'permissions')
        ], 200);
    }

    public function createRole(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:roles',
            'description' => 'nullable|string'
        ]);

        $role = Role::create($validated);

        return response()->json([
            'message' => 'Role created successfully',
            'role' => $role
        ], 201);
    }

    public function updateRole(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:roles,name,' . $role->id,
            'description' => 'nullable|string'
        ]);

        $role->update($validated);

        return response()->json([
            'message' => 'Role updated successfully',
            'role' => $role
        ], 200);
    }

    public function deleteRole(Role $role)
    {
        $role->delete();

        return response()->json([
            'message' => 'Role deleted successfully'
        ], 200);
    }

    public function assignPermissionToRole(Request $request, Role $role)
    {
        $validated = $request->validate([
            'permission_id' => 'required|exists:permissions,id'
        ]);

        $permission = Permission::find($validated['permission_id']);
        if (!$role->permissions()->where('permission_id', $permission->id)->exists()) {
            $role->permissions()->attach($permission);
        }

        return response()->json([
            'message' => 'Permission assigned to role',
            'role' => $role->load('permissions')
        ], 200);
    }

    public function removePermissionFromRole(Request $request, Role $role)
    {
        $validated = $request->validate([
            'permission_id' => 'required|exists:permissions,id'
        ]);

        $role->permissions()->detach($validated['permission_id']);

        return response()->json([
            'message' => 'Permission removed from role',
            'role' => $role->load('permissions')
        ], 200);
    }
}
