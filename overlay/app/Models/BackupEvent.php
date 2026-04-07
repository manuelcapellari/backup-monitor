<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BackupEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'computer_id',
        'status',
        'summary',
        'source_vendor',
        'event_at',
        'raw_email_ref',
    ];

    protected $casts = [
        'event_at' => 'datetime',
    ];

    public function computer(): BelongsTo
    {
        return $this->belongsTo(Computer::class);
    }
}
