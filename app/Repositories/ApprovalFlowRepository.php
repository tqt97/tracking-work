<?php

namespace App\Repositories;

use App\Models\ApprovalFlow;

class ApprovalFlowRepository extends BaseRepository
{
    public function __construct(ApprovalFlow $model)
    {
        parent::__construct($model);
    }

    /**
     * Lấy danh sách flow theo module và vai trò người gửi đơn
     */
    public function getFlow(string $module, string $role)
    {
        return $this->model
            ->where('module', $module)
            ->where('role', $role)
            ->orderBy('level')
            ->get();
    }
}
