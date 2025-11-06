<?php

namespace App\Services;

use App\Repositories\ReportRepository;
use Carbon\Carbon;

class ReportService extends BaseService
{
    public function __construct(ReportRepository $repository)
    {
        parent::__construct($repository);
    }

    public function generateMonthlyReport(string $month)
    {
        $attendance = $this->repository->getAttendanceSummary($month);
        $leave = $this->repository->getLeaveSummary($month);

        $reportData = [
            'month' => $month,
            'attendance_summary' => $attendance,
            'leave_summary' => $leave,
        ];

        return $this->repository->create([
            'type' => 'performance',
            'params' => ['month' => $month],
            'result' => $reportData,
            'generated_at' => Carbon::now(),
        ]);
    }

    public function getLatestReport(string $month)
    {
        return $this->repository->model
            ->whereJsonContains('params->month', $month)
            ->latest('generated_at')
            ->first();
    }
}
