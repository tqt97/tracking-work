<?php

namespace App\Services;

use App\Repositories\DepartmentRepository;
use App\Services\BaseService;
use Exception;

class DepartmentService extends BaseService
{
    public function __construct(DepartmentRepository $repository)
    {
        parent::__construct($repository);
    }

    public function getAllWithRelations()
    {
        return $this->repository->withManagerAndUsers();
    }

    public function assignManager(int $departmentId, int $userId)
    {
        $department = $this->repository->find($departmentId);
        $department->update(['manager_id' => $userId]);
        return $department;
    }
}
