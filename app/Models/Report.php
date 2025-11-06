<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $fillable = ['type', 'params', 'result', 'generated_at'];

    protected $casts = [
        'params' => 'array',
        'result' => 'array',
    ];
}
