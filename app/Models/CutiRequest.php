<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CutiRequest extends Model
{
    protected $fillable = ['employee_id', 'tanggal_awal', 'tanggal_akhir', 'pesan', 'status'];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function cutiHistories() {
        return $this->hasMany(CutiHistory::class);
    }
}
