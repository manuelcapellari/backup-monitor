<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RawEmail extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'mail_account_id',
        'message_uid',
        'message_id',
        'subject',
        'from_address',
        'received_at',
        'headers_json',
        'body_text',
        'body_html',
        'ingest_status',
        'ingest_error',
    ];

    protected $casts = [
        'received_at' => 'datetime',
        'headers_json' => 'array',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function mailAccount(): BelongsTo
    {
        return $this->belongsTo(MailAccount::class);
    }

    public function parserTraces(): HasMany
    {
        return $this->hasMany(ParserTrace::class);
    }
}
