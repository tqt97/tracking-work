<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use App\Services\LeaveRequestService;
use Illuminate\Http\Request;

class LeaveRequestController extends Controller
{
    private LeaveRequestService $service;

    public function __construct(LeaveRequestService $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = $this->service->getAll();

        return view('leave-request.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('leave-request.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->service->create($request->all());

        return redirect()->route('leave-requests.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(LeaveRequest $leaveRequest)
    {
        return view('leave-request.show', compact('leaveRequest'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(LeaveRequest $leaveRequest)
    {
        return view('leave-request.edit', compact('leaveRequest'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, LeaveRequest $leaveRequest)
    {
        $this->service->update($leaveRequest->id, $request->all());

        return redirect()->route('leave-requests.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LeaveRequest $leaveRequest)
    {
        $this->service->delete($leaveRequest->id);

        return redirect()->route('leave-requests.index');
    }

    public function approve($id, Request $request)
    {
        $approverId = $request->user()->id;
        $leave = $this->service->approveLeave($id, $approverId, $request->input('note'));

        return response()->json(['message' => 'Leave approved', 'data' => $leave]);
    }

    public function reject($id, Request $request)
    {
        $approverId = $request->user()->id;
        $leave = $this->service->rejectLeave($id, $approverId, $request->input('note'));

        return response()->json(['message' => 'Leave rejected', 'data' => $leave]);
    }
}
