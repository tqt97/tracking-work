<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;

class ApprovalFlowPageController extends Controller
{
    public function index()
    {
        $departments = Department::all(['id', 'name']);

        return view('admin.approval_flows.index', compact('departments'));
    }
}
