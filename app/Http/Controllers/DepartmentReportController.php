<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DepartmentReportController extends Controller
{
    public function leaveSummary(Request $request)
    {
        $month = $request->input('month', now()->format('Y-m'));
        $departmentId = $request->input('department_id');

        $query = DB::table('leave_requests as lr')
            ->join('users as u', 'u.id', '=', 'lr.user_id')
            ->join('departments as d', 'd.id', '=', 'u.department_id')
            ->select(
                'd.id as department_id',
                'd.name as department_name',
                DB::raw('COUNT(lr.id) as total_requests'),
                DB::raw('SUM(CASE WHEN lr.status = "approved" THEN 1 ELSE 0 END) as approved_count'),
                DB::raw('SUM(CASE WHEN lr.status = "rejected" THEN 1 ELSE 0 END) as rejected_count'),
                DB::raw('SUM(DATEDIFF(lr.end_date, lr.start_date) + 1) as total_days')
            )
            ->whereRaw("DATE_FORMAT(lr.start_date, '%Y-%m') = ?", [$month])
            ->groupBy('d.id', 'd.name');

        if ($departmentId) {
            $query->where('d.id', $departmentId);
        }

        return response()->json(['data' => $query->get()]);
    }
}
