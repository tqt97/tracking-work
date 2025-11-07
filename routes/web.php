<?php

use App\Http\Controllers\Admin\ApprovalFlowController;
use App\Http\Controllers\Admin\ApprovalFlowPageController;
use App\Http\Controllers\Admin\DepartmentPageController;
use App\Http\Controllers\Admin\DepartmentReportPageController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\DepartmentReportController;
use App\Http\Controllers\LeaveApprovalController;
use App\Http\Controllers\LeaveRequestController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\RolePermissionPageController;
use App\Http\Controllers\UserRoleController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

Route::prefix('attendances')->group(function () {
    Route::get('/', [AttendanceController::class, 'index'])->name('attendances.index');
    Route::post('/', [AttendanceController::class, 'store'])->name('attendances.store');
    Route::post('/check-in', [AttendanceController::class, 'checkIn'])->name('attendances.check-in');
    Route::post('/check-out', [AttendanceController::class, 'checkOut'])->name('attendances.check-out');
});

Route::prefix('leave-requests')->group(function () {
    Route::get('/', [LeaveRequestController::class, 'index'])->name('leave-requests.index');
    Route::post('/', [LeaveRequestController::class, 'store'])->name('leave-requests.store');
    Route::post('{id}/approve', [LeaveRequestController::class, 'approve'])->name('leave-requests.approve');
    Route::post('{id}/reject', [LeaveRequestController::class, 'reject'])->name('leave-requests.reject');
});

Route::prefix('reports')->group(function () {
    Route::post('/generate', [ReportController::class, 'generate'])->name('reports.generate');
    Route::get('/{month}', [ReportController::class, 'show'])->name('reports.show');
});

Route::prefix('leave-approvals')->group(function () {
    Route::get('/pending', [LeaveApprovalController::class, 'myPending'])
        ->name('approvals.pending');

    // ✅ Lấy danh sách tất cả người duyệt của 1 đơn cụ thể
    Route::get('/leave/{leaveRequestId}', [LeaveApprovalController::class, 'approvalsByRequest'])
        ->name('approvals.byLeaveRequest');

    // ✅ Phê duyệt đơn nghỉ (approvalId = id trong bảng leave_approvals)
    Route::post('/{id}/approve', [LeaveApprovalController::class, 'approve'])
        ->name('approvals.approve');

    // ✅ Từ chối đơn nghỉ
    Route::post('/{id}/reject', [LeaveApprovalController::class, 'reject'])
        ->name('approvals.reject');
});

Route::prefix('admin/approval-flows')->middleware('auth')->group(function () {
    Route::get('/', [ApprovalFlowController::class, 'index']);
    Route::post('/', [ApprovalFlowController::class, 'store']);
    Route::put('/{id}', [ApprovalFlowController::class, 'update']);
    Route::delete('/{id}', [ApprovalFlowController::class, 'destroy']);
});

Route::prefix('admin/departments')
    ->as('admin.departments')
    ->middleware('auth')
    ->group(function () {
        Route::get('/', [DepartmentController::class, 'index'])->name('index');
        Route::post('/', [DepartmentController::class, 'store'])->name('store');
        Route::put('/{id}', [DepartmentController::class, 'update'])->name('update');
        Route::delete('/{id}', [DepartmentController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/assign-manager', [DepartmentController::class, 'assignManager'])->name('assign-manager');
    });

Route::prefix('admin/reports')->middleware('auth')->group(function () {
    Route::get('/departments/leave-summary', [DepartmentReportController::class, 'leaveSummary'])->name('admin.departments.leave-summary');
});

Route::prefix('admin')->middleware(['auth', 'can:manage,App\Models\ApprovalFlow'])->group(function () {
    Route::get('/approval-flows', [ApprovalFlowPageController::class, 'index'])
        ->name('admin.approval_flows')
        ->middleware('permission:manage-flows');
    Route::get('/reports/departments', [DepartmentReportPageController::class, 'index'])
        ->name('admin.reports.departments')
        ->middleware('permission:view-reports');
    Route::get('/departments', [DepartmentPageController::class, 'index'])
        ->name('admin.departments')
        ->middleware('permission:assign-managers');
});

Route::prefix('admin')->middleware(['auth', 'permission:manage-flows'])->group(function () {
    Route::get('/roles/permissions', [RolePermissionPageController::class, 'index'])
        ->name('admin.roles.permissions');
});

Route::prefix('admin')->middleware(['auth', 'permission:manage-flows'])->group(function () {
    Route::get('/roles', [RoleController::class, 'index'])->name('admin.roles.index');
    Route::post('/roles', [RoleController::class, 'store'])->name('admin.roles.store');
    Route::put('/roles/{role}', [RoleController::class, 'update'])->name('admin.roles.update');
    Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->name('admin.roles.destroy');
});

// Route::prefix('admin')->middleware(['auth', 'permission:manage-flows'])->group(function () {
//     Route::get('/users/roles', [UserRoleController::class, 'index'])->name('admin.users.roles');
//     Route::post('/users/{user}/roles', [UserRoleController::class, 'syncRoles'])->name('admin.users.roles.sync');
// });
Route::prefix('admin')->middleware(['auth', 'permission:manage-flows'])->group(function () {
    Route::get('/users/roles', [UserRoleController::class, 'index'])->name('admin.users.roles');
    Route::post('/users/{user}/roles', [UserRoleController::class, 'syncRoles'])->name('admin.users.roles.sync');
    Route::post('/users/roles/bulk-assign', [UserRoleController::class, 'bulkAssign'])->name('admin.users.roles.bulk');
    Route::get('/users/roles/export', [UserRoleController::class, 'exportCsv'])->name('admin.users.roles.export');
    Route::post('/users/roles/import', [UserRoleController::class, 'importCsv'])->name('admin.users.roles.import');
});
