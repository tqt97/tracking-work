<?php

namespace App\Services;

use App\Repositories\LeaveApprovalRepository;
use Exception;
use Illuminate\Support\Facades\DB;

class LeaveApprovalService extends BaseService
{
    public function __construct(LeaveApprovalRepository $repository)
    {
        parent::__construct($repository);
    }

    /**
     * Danh sách các đơn chờ phê duyệt của người duyệt hiện tại
     */
    public function getMyPending(int $approverId)
    {
        return $this->repository->getPendingApprovals($approverId);
    }

    /**
     * Lấy danh sách cấp duyệt của 1 đơn cụ thể
     */
    public function getByLeaveRequest(int $leaveRequestId)
    {
        return $this->repository->getByLeaveRequest($leaveRequestId);
    }

    /**
     * Phê duyệt đơn
     */
    public function approve(int $approvalId, int $approverId, ?string $note = null)
    {
        return DB::transaction(function () use ($approvalId, $approverId, $note) {
            $approval = $this->repository->find($approvalId);

            if ($approval->approver_id !== $approverId) {
                throw new Exception('Bạn không có quyền duyệt đơn này.');
            }

            $approval->update([
                'status' => 'approved',
                'note' => $note,
            ]);

            // Nếu tất cả cấp duyệt đã approved thì cập nhật trạng thái đơn chính
            $leaveRequest = $approval->leaveRequest;
            $allApproved = $leaveRequest->approvals()->where('status', '!=', 'approved')->count() === 0;
            if ($allApproved) {
                $leaveRequest->update(['status' => 'approved']);
            }

            return $approval;
        });
    }

    /**
     * Từ chối đơn
     */
    public function reject(int $approvalId, int $approverId, ?string $note = null)
    {
        return DB::transaction(function () use ($approvalId, $approverId, $note) {
            $approval = $this->repository->find($approvalId);

            if ($approval->approver_id !== $approverId) {
                throw new Exception('Bạn không có quyền từ chối đơn này.');
            }

            $approval->update([
                'status' => 'rejected',
                'note' => $note,
            ]);

            // Cập nhật luôn đơn chính là rejected
            $approval->leaveRequest->update(['status' => 'rejected']);

            return $approval;
        });
    }
}
