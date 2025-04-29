<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionController extends Controller
{
    public function index()
    {
        $pageTitle = 'Permissions';
        $permissions = Permission::where('guard_name', 'admin')->get();
        return view('admin.permissions.index', compact('permissions', 'pageTitle'));
    }

    public function create()
    {
        $pageTitle = 'Create Permission';
        return view('admin.permissions.create', compact('pageTitle'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:permissions,name',
        ]);

        Permission::create([
            'name' => $request->name,
            'guard_name' => 'admin'
        ]);

        return redirect()->route('admin.permissions.index')->with('success', 'Permission created successfully.');
    }

    public function edit($id)
    {
        $pageTitle = 'Edit Permission';
        $permission = Permission::findOrFail($id);
        return view('admin.permissions.edit', compact('permission', 'pageTitle'));
    }

    public function update(Request $request, $id)
    {
        $permission = Permission::findOrFail($id);

        $request->validate([
            'name' => 'required|unique:permissions,name,' . $permission->id,
        ]);

        $permission->update(['name' => $request->name]);

        return redirect()->route('admin.permissions.index')->with('success', 'Permission updated successfully.');
    }

    public function destroy($id)
    {
        $permission = Permission::findOrFail($id);
        $permission->delete();

        return redirect()->route('admin.permissions.index')->with('success', 'Permission deleted successfully.');
    }

    public function assignPermissionsToRole(Request $request, $roleId)
    {
        //$pageTitle = 'Assign Permissions to Role';
        $role = Role::findOrFail($roleId);
        $permissions = $request->permissions;

        $role->syncPermissions($permissions);

        return redirect()->route('admin.roles.edit', $roleId)->with('success', 'Permissions updated successfully.');
    }
}
