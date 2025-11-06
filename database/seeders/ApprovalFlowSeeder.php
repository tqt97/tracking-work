<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ApprovalFlowSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ví dụ: nhân viên (staff) -> manager -> HR
        $staff = Role::where('slug', 'staff')->first();
        $manager = Role::where('slug', 'manager')->first();
        $admin = Role::where('slug', 'admin')->first();

        DB::table('approval_flows')->insert([
            [
                'module' => 'leave',
                'role' => 'staff',
                'level' => 1,
                'approver_role_id' => $manager->id,
            ],
            [
                'module' => 'leave',
                'role' => 'staff',
                'level' => 2,
                'approver_role_id' => $admin->id,
            ],
        ]);
    }
}
