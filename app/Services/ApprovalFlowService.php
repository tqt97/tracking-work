<?php

namespace App\Services;

use App\Models\LeaveApproval;
use App\Models\User;
use App\Repositories\ApprovalFlowRepository;
use Exception;
use Illuminate\Support\Facades\DB;

class ApprovalFlowService
{
    protected ApprovalFlowRepository $repository;

    public function __construct(ApprovalFlowRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Tự động sinh danh sách người duyệt dựa trên flow cấu hình
     */
    public function generateForLeave(int $leaveRequestId, User $requester)
    {
        $userRole = $requester->roles()->first()?->slug ?? 'staff';
        $flows = $this->repository->getFlow('leave', $userRole);

        if ($flows->isEmpty()) {
            throw new Exception("Không tìm thấy flow phê duyệt cho vai trò: {$userRole}");
        }

        $approvals = [];
        foreach ($flows as $flow) {
            // Lấy danh sách user có role tương ứng
            $approvers = User::whereHas('roles', function ($q) use ($flow) {
                $q->where('roles.id', $flow->approver_role_id);
            })->get();

            foreach ($approvers as $approver) {
                $approvals[] = [
                    'leave_request_id' => $leaveRequestId,
                    'approver_id' => $approver->id,
                    'level' => $flow->level,
                    'status' => 'pending',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        DB::table('leave_approvals')->insert($approvals);

        foreach ($approvals as $a) {
            event(new \App\Events\LeaveApprovalCreated(LeaveApproval::find($a['id'] ?? null)));
        }
    }
}
