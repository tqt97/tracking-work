<?php

namespace App\Repositories;

use App\Models\Attendance;
use App\Repositories\BaseRepository;

class AttendanceRepository extends BaseRepository
{
    public function __construct(Attendance $model)
    {
        parent::__construct($model);
    }

    public function getUserAttendance(int $userId)
    {
        return $this->model
            ->where('user_id', $userId)
            ->orderByDesc('date')
            ->get();
    }

    public function getReportByMonth(int $userId, string $month)
    {
        return $this->model
            ->where('user_id', $userId)
            ->whereMonth('date', $month)
            ->selectRaw('SUM(total_hours) as total_hours, COUNT(*) as days')
            ->first();
    }
}
