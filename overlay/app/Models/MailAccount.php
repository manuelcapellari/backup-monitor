<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Crypt;

class MailAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'name',
        'protocol',
        'host',
        'port',
        'encryption',
        'username',
        'password_encrypted',
        'mailbox',
        'poll_interval_minutes',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'bool',
        'poll_interval_minutes' => 'int',
        'port' => 'int',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function rawEmails(): HasMany
    {
        return $this->hasMany(RawEmail::class);
    }

    public function mailboxPathFlag(): string
    {
        return match ($this->encryption) {
            'ssl' => '/imap/ssl',
            'tls' => '/imap/tls',
            default => '/imap/notls',
        };
    }

    public function decryptedPassword(): string
    {
        if (! $this->password_encrypted) {
            return '';
        }

        try {
            return Crypt::decryptString($this->password_encrypted);
        } catch (\Throwable) {
            return '';
        }
    }
}
