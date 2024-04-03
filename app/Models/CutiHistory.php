<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CutiHistory extends Model
{
    protected $fillable = ['cuti_request_id', 'action', 'employee_id', 'alasan'];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function cutiRequest(): BelongsTo
    {
        return $this->belongsTo(CutiRequest::class);
    }
}
