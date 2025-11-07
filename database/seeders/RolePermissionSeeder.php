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
        // $permissions = [
        //     'manage_users',
        //     'manage_attendance',
        //     'manage_leave',
        //     'manage_notifications',
        //     'view_reports',
        // ];

        // foreach ($permissions as $perm) {
        //     Permission::firstOrCreate(['slug' => $perm], [
        //         'name' => ucwords(str_replace('_', ' ', $perm)),
        //     ]);
        // }

        // // Tạo các vai trò
        // $roles = [
        //     'admin' => ['*'],
        //     'manager' => ['manage_leave', 'view_reports'],
        //     'staff' => ['manage_attendance'],
        // ];

        // foreach ($roles as $role => $perms) {
        //     $r = Role::firstOrCreate(['slug' => $role], ['name' => ucfirst($role)]);
        //     $assign = $perms[0] === '*' ? Permission::all() : Permission::whereIn('slug', $perms)->get();
        //     $r->permissions()->sync($assign->pluck('id'));
        // }

        $roles = [
            ['name' => 'Admin', 'slug' => 'admin', 'description' => 'Toàn quyền hệ thống'],
            ['name' => 'Manager', 'slug' => 'manager', 'description' => 'Quản lý phòng ban'],
            ['name' => 'Staff', 'slug' => 'staff', 'description' => 'Nhân viên bình thường'],
        ];

        $permissions = [
            ['name' => 'Quản lý flow phê duyệt', 'slug' => 'manage-flows'],
            ['name' => 'Xem báo cáo', 'slug' => 'view-reports'],
            ['name' => 'Gán trưởng phòng', 'slug' => 'assign-managers'],
            ['name' => 'Duyệt đơn nghỉ', 'slug' => 'approve-leaves'],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['slug' => $role['slug']], $role);
        }

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['slug' => $permission['slug']], $permission);
        }

        $admin = Role::where('slug', 'admin')->first();
        $manager = Role::where('slug', 'manager')->first();

        $admin->permissions()->sync(Permission::all()->pluck('id'));
        $manager->permissions()->sync(Permission::whereIn('slug', ['view-reports', 'approve-leaves'])->pluck('id'));
    }
}
