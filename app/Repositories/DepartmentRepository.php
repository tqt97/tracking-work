<?php

namespace App\Repositories;

use App\Models\Department;

class DepartmentRepository extends BaseRepository
{
    public function __construct(Department $model)
    {
        parent::__construct($model);
    }

    public function withManagerAndUsers()
    {
        return $this->model->with(['manager:id,name,email', 'users:id,name,email,department_id'])->get();
    }
}
