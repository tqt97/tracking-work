<?php

namespace App\Repositories;

use App\Models\Report;
use Illuminate\Support\Facades\DB;

class ReportRepository extends BaseRepository
{
    public function __construct(Report $model)
    {
        parent::__construct($model);
    }

    public function getAttendanceSummary(string $month)
    {
        return DB::table('attendances')
            ->join('users', 'attendances.user_id', '=', 'users.id')
            ->select(
                'users.name',
                DB::raw('SUM(total_hours) as total_hours'),
                DB::raw('COUNT(*) as working_days')
            )
            ->whereMonth('attendances.date', $month)
            ->groupBy('users.name')
            ->get();
    }

    public function getLeaveSummary(string $month)
    {
        return DB::table('leave_requests')
            ->join('users', 'leave_requests.user_id', '=', 'users.id')
            ->select(
                'users.name',
                DB::raw('COUNT(*) as total_leaves'),
                DB::raw('SUM(DATEDIFF(end_date, start_date) + 1) as total_days')
            )
            ->whereMonth('leave_requests.start_date', $month)
            ->where('leave_requests.status', 'approved')
            ->groupBy('users.name')
            ->get();
    }
}
