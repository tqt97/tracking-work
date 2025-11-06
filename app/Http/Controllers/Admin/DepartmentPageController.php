<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;

class DepartmentPageController extends Controller
{
    public function index()
    {
        $departments = Department::with('manager')->get();

        return view('admin.departments.index', compact('departments'));
    }
}
