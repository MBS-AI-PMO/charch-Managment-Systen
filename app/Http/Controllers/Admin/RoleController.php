<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\RoleRequest;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::where('guard_name', 'admin')->withCount('users')->orderBy('name')->get();
        $permissions = Permission::where('guard_name', 'admin')->orderBy('name')->get();
        return view('admin.roles.index', compact('roles', 'permissions'));
    }

    public function create()
    {
        $role = new Role(['guard_name' => 'admin']);
        $permissions = Permission::where('guard_name', 'admin')->orderBy('name')->get();
        return view('admin.roles.edit', compact('role', 'permissions'));
    }

    public function store(RoleRequest $req)
    {
        $data = $req->validated();
        $role = Role::create(['name' => $data['name'], 'guard_name' => 'admin']);
        $role->syncPermissions($data['permissions'] ?? []);
        return redirect()->route('admin.roles.index')->with('success', 'Role created.');
    }

    public function edit(Role $role)
    {
        $permissions = Permission::where('guard_name', 'admin')->orderBy('name')->get();
        return view('admin.roles.edit', compact('role', 'permissions'));
    }

    public function update(RoleRequest $req, Role $role)
    {
        if ($role->name === 'Site Admin' && ($req->input('name') !== 'Site Admin')) {
            throw ValidationException::withMessages(['name' => 'Cannot rename the Site Admin role.']);
        }
        $data = $req->validated();
        $role->update(['name' => $data['name']]);
        // Always keep Site Admin holding every permission.
        $perms = $role->name === 'Site Admin'
            ? Permission::where('guard_name', 'admin')->pluck('name')->all()
            : ($data['permissions'] ?? []);
        $role->syncPermissions($perms);
        return redirect()->route('admin.roles.edit', $role)->with('success', 'Role updated.');
    }

    public function show(Role $role)
    {
        return redirect()->route('admin.roles.edit', $role);
    }

    public function destroy(Role $role)
    {
        if ($role->name === 'Site Admin') {
            throw ValidationException::withMessages(['role' => 'Cannot delete the Site Admin role.']);
        }
        $role->delete();
        return redirect()->route('admin.roles.index')->with('success', 'Role deleted.');
    }
}
