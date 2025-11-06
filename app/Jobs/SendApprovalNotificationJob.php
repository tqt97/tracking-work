<?php

namespace App\Jobs;

use App\Models\LeaveApproval;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class SendApprovalNotificationJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(protected LeaveApproval $approval)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $approver = $this->approval->approver;
        $leave = $this->approval->leaveRequest;

        // Gửi email (có thể thay bằng Slack, SMS,…)
        Mail::raw(
            "Xin chào {$approver->name},\nBạn có 1 đơn nghỉ phép mới cần duyệt từ {$leave->user->name}.",
            fn ($msg) => $msg->to($approver->email)->subject('Thông báo phê duyệt nghỉ phép')
        );

        $hrEmails = ['hr@company.com'];
        foreach ($hrEmails as $email) {
            Mail::raw(
                "Thông báo: có đơn nghỉ mới của {$leave->user->name}.",
                fn ($msg) => $msg->to($email)->subject('[HR] Đơn nghỉ mới')
            );
        }
    }
}
