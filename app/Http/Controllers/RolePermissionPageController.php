<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;

class RolePermissionPageController extends Controller
{
    public function index()
    {
        $roles = Role::with('permissions:id')->get(['id', 'name']);
        $permissions = Permission::all(['id', 'name', 'slug']);

        return view('admin.roles.permissions', compact('roles', 'permissions'));
    }
}
