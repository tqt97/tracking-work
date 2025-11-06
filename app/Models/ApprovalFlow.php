<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApprovalFlow extends Model
{
    /** @use HasFactory<\Database\Factories\ApprovalFlowFactory> */
    use HasFactory;

    protected $fillable = [
        'module', 'role', 'level', 'approver_role_id',
    ];

    public function approverRole(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'approver_role_id');
    }
}
