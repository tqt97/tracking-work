<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\LeaveRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::factory(10)->create();

        $roles = Role::all();
        foreach ($users as $user) {
            $user->roles()->attach($roles->random()->id);
        }

        foreach ($users as $user) {
            Attendance::factory(10)->create(['user_id' => $user->id]);
        }

        foreach ($users as $user) {
            LeaveRequest::factory(3)->create(['user_id' => $user->id]);
        }

        // foreach ($users as $user) {
        //     Notification::factory(5)->create(['user_id' => $user->id]);
        // }
    }
}
