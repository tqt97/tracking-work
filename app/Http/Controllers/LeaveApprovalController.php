<?php

namespace App\Http\Controllers;

use App\Services\LeaveApprovalService;
use Illuminate\Http\Request;

class LeaveApprovalController extends Controller
{
    private LeaveApprovalService $service;

    public function __construct(LeaveApprovalService $service)
    {
        $this->service = $service;
    }

    /**
     * Danh sách các đơn đang chờ người này duyệt
     */
    public function myPending(Request $request)
    {
        $approverId = $request->user()->id;
        $data = $this->service->getMyPending($approverId);

        return response()->json(['data' => $data]);
    }

    /**
     * Danh sách các cấp duyệt của một đơn cụ thể
     */
    public function approvalsByRequest($leaveRequestId)
    {
        $data = $this->service->getByLeaveRequest($leaveRequestId);

        return response()->json(['data' => $data]);
    }

    /**
     * Phê duyệt
     */
    public function approve($id, Request $request)
    {
        $approval = $this->service->find($id);
        $this->authorize('approve', $approval);

        $approverId = $request->user()->id;
        $note = $request->input('note');
        $approval = $this->service->approve($id, $approverId, $note);

        return response()->json(['message' => 'Đã phê duyệt đơn nghỉ phép', 'data' => $approval]);
    }

    /**
     * Từ chối
     */
    public function reject($id, Request $request)
    {
        $approverId = $request->user()->id;
        $note = $request->input('note');
        $approval = $this->service->reject($id, $approverId, $note);

        return response()->json(['message' => 'Đã từ chối đơn nghỉ phép', 'data' => $approval]);
    }
}
