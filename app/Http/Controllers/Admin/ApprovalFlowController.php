<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\ApprovalFlowRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApprovalFlowController extends Controller
{
    private ApprovalFlowRepository $repository;

    public function __construct(ApprovalFlowRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Danh sách flow hiện tại (lọc theo module/department)
     */
    public function index(Request $request)
    {
        $query = DB::table('approval_flows')
            ->join('roles as r', 'r.id', '=', 'approval_flows.approver_role_id')
            ->select('approval_flows.*', 'r.name as approver_role_name');

        if ($request->has('module')) {
            $query->where('approval_flows.module', $request->input('module'));
        }

        if ($request->has('department_id')) {
            $query->where('approval_flows.department_id', $request->input('department_id'));
        }

        return response()->json(['data' => $query->get()]);
    }

    /**
     * Tạo mới flow duyệt
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'module' => 'required|string',
            'role' => 'required|string',
            'department_id' => 'nullable|integer',
            'level' => 'required|integer|min:1',
            'approver_role_id' => 'required|integer|exists:roles,id',
        ]);

        $flow = $this->repository->create($data);

        return response()->json(['message' => 'Flow duyệt đã được tạo.', 'data' => $flow]);
    }

    /**
     * Cập nhật flow duyệt
     */
    public function update(Request $request, $id)
    {
        $data = $request->only(['level', 'approver_role_id']);
        $flow = $this->repository->update($id, $data);

        return response()->json(['message' => 'Đã cập nhật flow duyệt.', 'data' => $flow]);
    }

    /**
     * Xóa flow
     */
    public function destroy($id)
    {
        $this->repository->delete($id);

        return response()->json(['message' => 'Đã xóa flow duyệt.']);
    }
}
