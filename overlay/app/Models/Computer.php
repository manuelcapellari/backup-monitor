<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Computer extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'hostname',
        'display_name',
        'last_status',
        'last_event_at',
    ];

    protected $casts = [
        'last_event_at' => 'datetime',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function aliases(): HasMany
    {
        return $this->hasMany(ComputerAlias::class);
    }

    public function backupEvents(): HasMany
    {
        return $this->hasMany(BackupEvent::class);
    }
}
