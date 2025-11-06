<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
            ['name' => 'Phòng Kỹ thuật', 'code' => 'ENG', 'description' => 'Thiết kế và phát triển sản phẩm'],
            ['name' => 'Phòng Kinh doanh', 'code' => 'SALE', 'description' => 'Bán hàng và chăm sóc khách hàng'],
            ['name' => 'Phòng Nhân sự', 'code' => 'HR', 'description' => 'Quản lý nhân sự và tuyển dụng'],
        ];

        foreach ($departments as $dep) {
            Department::firstOrCreate(['code' => $dep['code']], $dep);
        }
    }
}
