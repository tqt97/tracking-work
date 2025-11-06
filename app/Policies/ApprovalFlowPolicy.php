<?php

namespace App\Policies;

use App\Models\User;

class ApprovalFlowPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Kiểm tra quyền quản lý flow phê duyệt
     */
    public function manage(User $user): bool
    {
        // Giả định: user có role 'admin' hoặc có permission manage-flows
        return $user->role === 'admin' || $user->hasPermission('manage-flows');
    }

    /**
     * Mọi người đều có thể xem flow
     */
    public function viewAny(User $user): bool
    {
        return true;
    }
}
