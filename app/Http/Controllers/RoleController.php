<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::query()->latest()->get(['id', 'name', 'slug', 'description']);

        return view('admin.roles.index', compact('roles'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|max:100',
            'slug' => 'required|max:100|unique:roles,slug',
            'description' => 'nullable|string',
        ]);

        Role::create($data);

        return redirect()->back()->with('success', 'Đã thêm vai trò mới.');
    }

    public function update(Request $request, Role $role)
    {
        if ($role->slug === 'admin') {
            return redirect()->back()->with('error', 'Không thể chỉnh sửa vai trò admin.');
        }

        $data = $request->validate([
            'name' => 'required|max:100',
            'description' => 'nullable|string',
        ]);

        $role->update($data);

        return redirect()->back()->with('success', 'Đã cập nhật vai trò.');
    }

    public function destroy(Role $role)
    {
        if ($role->slug === 'admin') {
            return redirect()->back()->with('error', 'Không thể xóa vai trò admin.');
        }

        $role->delete();

        return redirect()->back()->with('success', 'Đã xóa vai trò.');
    }
}
