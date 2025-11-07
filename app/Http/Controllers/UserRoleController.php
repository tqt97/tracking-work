<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class UserRoleController extends Controller
{
    public function index(Request $request): View
    {
        // return response()->json(['data' => User::with('roles')->get()]);
        // $users = User::with('roles:id,name')->get(['id', 'name', 'email']);
        // $roles = Role::all(['id', 'name']);
        // return view('admin.users.roles', compact('users', 'roles'));
        $roles = Role::select('id', 'name')->get();
        $filterRole = $request->query('role');

        $users = User::with('roles:id,name')->get(['id', 'name', 'email'])->map(function ($u) {
            return [
                'id' => $u->id,
                'name' => $u->name,
                'email' => $u->email,
                'roles' => $u->roles->map(fn ($r) => ['id' => $r->id, 'name' => $r->name]),
                'selected_roles' => $u->roles->pluck('id'),
            ];
        });

        if ($filterRole) {
            $users = $users->filter(fn ($u) => $u['selected_roles']->contains($filterRole))->values();
        }

        return view('admin.users.roles', [
            'rolesJson' => $roles->toJson(),
            'usersJson' => $users->toJson(),
            'filterRole' => $filterRole,
        ]);
    }

    public function syncRoles(Request $request, User $user)
    {
        $data = $request->validate(['role_id' => 'required|integer|exists:roles,id']);
        $user->roles()->sync([$data['role_id']]);

        return response()->json(['message' => 'Đã gán vai trò cho người dùng.']);
    }

    public function bulkAssign(Request $request)
    {
        $data = $request->validate([
            'user_ids' => 'required|array',
            'role_id' => 'required|integer|exists:roles,id',
        ]);

        foreach ($data['user_ids'] as $id) {
            $user = User::find($id);
            if ($user) {
                $user->roles()->syncWithoutDetaching([$data['role_id']]);
            }
        }

        return response()->json(['message' => 'Đã gán vai trò hàng loạt.']);
    }

    public function exportCsv(): StreamedResponse
    {
        $filename = 'user_roles_'.now()->format('Ymd_His').'.csv';
        $users = User::with('roles:id,name')->get();

        $callback = function () use ($users) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['User ID', 'Name', 'Email', 'Roles']);
            foreach ($users as $user) {
                fputcsv($handle, [
                    $user->id,
                    $user->name,
                    $user->email,
                    $user->roles->pluck('name')->join(', '),
                ]);
            }
            fclose($handle);
        };

        return response()->streamDownload($callback, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    public function importCsv(Request $request): RedirectResponse
    {
        $request->validate(['file' => 'required|file|mimes:csv,txt']);
        $rows = array_map('str_getcsv', file($request->file('file')->getRealPath()));
        array_shift($rows); // remove header

        foreach ($rows as $row) {
            [$id, $name, $email, $roles] = $row;
            $user = User::where('email', $email)->first();
            if ($user) {
                $roleNames = array_map('trim', explode(',', $roles));
                $roleIds = Role::whereIn('name', $roleNames)->pluck('id');
                $user->roles()->sync($roleIds);
            }
        }

        return back()->with('success', 'Đã import CSV thành công.');
    }
}
