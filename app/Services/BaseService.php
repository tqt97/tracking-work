<?php

namespace App\Services;

use App\Repositories\Contracts\BaseRepositoryInterface;
use Exception;
use Illuminate\Support\Facades\Log;

abstract class BaseService
{
    protected BaseRepositoryInterface $repository;

    public function __construct(BaseRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function getAll()
    {
        return $this->repository->all();
    }

    public function find(int $id)
    {
        return $this->repository->find($id);
    }

    public function create(array $data)
    {
        try {
            return $this->repository->create($data);
        } catch (Exception $e) {
            Log::error('Create failed: '.$e->getMessage());
            throw $e;
        }
    }

    public function update(int $id, array $data)
    {
        try {
            return $this->repository->update($id, $data);
        } catch (Exception $e) {
            Log::error('Update failed: '.$e->getMessage());
            throw $e;
        }
    }

    public function delete(int $id)
    {
        return $this->repository->delete($id);
    }
}
