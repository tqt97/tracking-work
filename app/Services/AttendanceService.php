<?php

namespace App\Services;

use App\Repositories\AttendanceRepository;
use App\Services\BaseService;
use Carbon\Carbon;

class AttendanceService extends BaseService
{
    public function __construct(AttendanceRepository $repository)
    {
        parent::__construct($repository);
    }

    public function checkIn(int $userId): void
    {
        $today = Carbon::today()->toDateString();
        $exists = $this->repository->model->where('user_id', $userId)->where('date', $today)->exists();

        if ($exists) {
            throw new \Exception('Already checked in today.');
        }

        $this->repository->create([
            'user_id' => $userId,
            'date' => $today,
            'check_in' => now(),
            'status' => 'on_time',
        ]);
    }

    public function checkOut(int $userId): void
    {
        $today = Carbon::today()->toDateString();
        $attendance = $this->repository->model
            ->where('user_id', $userId)
            ->where('date', $today)
            ->firstOrFail();

        $attendance->update([
            'check_out' => now(),
            'total_hours' => now()->diffInHours($attendance->check_in),
        ]);
    }
}
