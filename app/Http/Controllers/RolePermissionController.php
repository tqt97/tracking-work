<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RolePermissionController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'roles' => Role::with('permissions')->get(),
            'permissions' => Permission::all(),
        ]);
    }

    public function syncPermissions(Request $request, Role $role): JsonResponse
    {
        $data = $request->validate(['permission_ids' => 'required|array']);
        $role->permissions()->sync($data['permission_ids']);

        return response()->json(['message' => 'Đã cập nhật quyền cho vai trò.']);
    }
}
