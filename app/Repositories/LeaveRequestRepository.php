<?php

namespace App\Modules\Leave\Repositories;

use App\Models\LeaveRequest;
use App\Repositories\BaseRepository;

class LeaveRequestRepository extends BaseRepository
{
    public function __construct(LeaveRequest $model)
    {
        parent::__construct($model);
    }

    public function getUserLeaves(int $userId)
    {
        return $this->model->where('user_id', $userId)->latest()->get();
    }

    public function getPendingApprovals(int $approverId)
    {
        return $this->model->whereHas('approvals', function ($q) use ($approverId) {
            $q->where('approver_id', $approverId)->where('status', 'pending');
        })->get();
    }
}
