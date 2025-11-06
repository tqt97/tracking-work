<?php

namespace App\Http\Controllers;

use App\Services\ReportService;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    private ReportService $service;

    public function __construct(ReportService $service)
    {
        $this->service = $service;
    }

    public function generate(Request $request)
    {
        $month = $request->input('month', now()->format('m'));
        $report = $this->service->generateMonthlyReport($month);

        return response()->json(['message' => 'Report generated successfully', 'data' => $report]);
    }

    public function show($month)
    {
        $report = $this->service->getLatestReport($month);

        return response()->json(['data' => $report]);
    }
}
