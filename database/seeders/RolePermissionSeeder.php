<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tạo các quyền cơ bản
        $permissions = [
            'manage_users',
            'manage_attendance',
            'manage_leave',
            'manage_notifications',
            'view_reports',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['slug' => $perm], [
                'name' => ucwords(str_replace('_', ' ', $perm)),
            ]);
        }

        // Tạo các vai trò
        $roles = [
            'admin' => ['*'],
            'manager' => ['manage_leave', 'view_reports'],
            'staff' => ['manage_attendance'],
        ];

        foreach ($roles as $role => $perms) {
            $r = Role::firstOrCreate(['slug' => $role], ['name' => ucfirst($role)]);
            $assign = $perms[0] === '*' ? Permission::all() : Permission::whereIn('slug', $perms)->get();
            $r->permissions()->sync($assign->pluck('id'));
        }
    }
}
