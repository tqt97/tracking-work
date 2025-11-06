<?php

namespace App\Http\Controllers;

use App\Models\ApprovaFlow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApprovaFlowController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = DB::table('approval_flows')
            ->join('roles as r', 'r.id', '=', 'approval_flows.approver_role_id')
            ->leftJoin('departments as d', 'd.id', '=', 'approval_flows.department_id')
            ->select('approval_flows.*', 'r.name as approver_role_name', 'd.name as department_name');

        if ($request->has('department_id')) {
            $query->where('approval_flows.department_id', $request->department_id);
        }

        if ($request->has('module')) {
            $query->where('approval_flows.module', $request->module);
        }

        return response()->json(['data' => $query->orderBy('level')->get()]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(ApprovaFlow $approvaFlow)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ApprovaFlow $approvaFlow)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ApprovaFlow $approvaFlow)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ApprovaFlow $approvaFlow)
    {
        //
    }
}
