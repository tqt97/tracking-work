<?php

namespace App\Repositories;

use App\Models\LeaveApproval;

class LeaveApprovalRepository extends BaseRepository
{
    public function __construct(LeaveApproval $model)
    {
        parent::__construct($model);
    }

    /**
     * Lấy danh sách phê duyệt của 1 đơn nghỉ phép
     */
    public function getByLeaveRequest(int $leaveRequestId)
    {
        return $this->model
            ->with('approver:id,name,email')
            ->where('leave_request_id', $leaveRequestId)
            ->orderBy('level')
            ->get();
    }

    /**
     * Lấy danh sách tất cả đơn cần duyệt của người dùng hiện tại
     */
    public function getPendingApprovals(int $approverId)
    {
        return $this->model
            ->with('leaveRequest')
            ->where('approver_id', $approverId)
            ->where('status', 'pending')
            ->get();
    }
}
