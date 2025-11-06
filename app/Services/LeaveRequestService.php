<?php

namespace App\Modules\Leave\Services;

use App\Models\LeaveApproval;
use App\Modules\Leave\Repositories\LeaveRequestRepository;
use App\Services\BaseService;
use Illuminate\Support\Facades\DB;

class LeaveRequestService extends BaseService
{
    public function __construct(LeaveRequestRepository $repository)
    {
        parent::__construct($repository);
    }

    public function submitLeave(array $data)
    {
        return DB::transaction(function () use ($data) {
            $leave = $this->repository->create($data);

            // Gán người duyệt mặc định (có thể sau này dùng rule động)
            LeaveApproval::create([
                'leave_request_id' => $leave->id,
                'approver_id' => 1, // Admin mặc định
                'level' => 1,
                'status' => 'pending',
            ]);

            return $leave;
        });
    }

    public function approveLeave(int $leaveId, int $approverId, string $note = '')
    {
        $leave = $this->repository->find($leaveId);
        $approval = LeaveApproval::where('leave_request_id', $leaveId)
            ->where('approver_id', $approverId)
            ->firstOrFail();

        $approval->update(['status' => 'approved', 'note' => $note]);
        $leave->update(['status' => 'approved']);

        return $leave;
    }

    public function rejectLeave(int $leaveId, int $approverId, string $note = '')
    {
        $leave = $this->repository->find($leaveId);
        $approval = LeaveApproval::where('leave_request_id', $leaveId)
            ->where('approver_id', $approverId)
            ->firstOrFail();

        $approval->update(['status' => 'rejected', 'note' => $note]);
        $leave->update(['status' => 'rejected']);

        return $leave;
    }
}
