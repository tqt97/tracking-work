<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;

class DepartmentReportPageController extends Controller
{
    public function index()
    {
        $departments = Department::all(['id', 'name']);

        return view('admin.reports.departments', compact('departments'));
    }
}
