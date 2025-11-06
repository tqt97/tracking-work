<?php

namespace App\Http\Controllers;

use App\Services\DepartmentService;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    private DepartmentService $service;

    public function __construct(DepartmentService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        return response()->json(['data' => $this->service->getAllWithRelations()]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'code' => 'required|string|max:50|unique:departments,code',
            'description' => 'nullable|string',
        ]);

        $department = $this->service->create($data);
        return response()->json(['message' => 'Tạo phòng ban thành công', 'data' => $department]);
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'name' => 'string|max:150',
            'description' => 'nullable|string',
        ]);

        $department = $this->service->update($id, $data);
        return response()->json(['message' => 'Cập nhật phòng ban thành công', 'data' => $department]);
    }

    public function destroy($id)
    {
        $this->service->delete($id);
        return response()->json(['message' => 'Đã xóa phòng ban.']);
    }

    public function assignManager(Request $request, $id)
    {
        $data = $request->validate(['manager_id' => 'required|integer|exists:users,id']);
        $department = $this->service->assignManager($id, $data['manager_id']);
        return response()->json(['message' => 'Đã gán trưởng phòng thành công', 'data' => $department]);
    }
}
