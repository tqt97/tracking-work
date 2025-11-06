<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\LeaveApprovalController;
use App\Http\Controllers\LeaveRequestController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
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
