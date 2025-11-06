<?php

namespace App\Policies;

use App\Models\LeaveApproval;
use App\Models\User;

class LeaveApprovalPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Check if user can approve leave approval
     */
    public function approve(User $user, LeaveApproval $approval): bool
    {
        // Là người phê duyệt chính
        if ($approval->approver_id === $user->id) {
            return true;
        }

        // Là trưởng phòng của nhân viên trong cùng department
        return $user->department_id === $approval->leaveRequest->user->department_id
            && $user->id === $user->department->manager_id;
    }
}
