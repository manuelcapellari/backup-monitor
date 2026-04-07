<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ParserTrace extends Model
{
    use HasFactory;

    protected $fillable = [
        'raw_email_id',
        'parser_rule_id',
        'matched',
        'reason',
        'result_json',
    ];

    protected $casts = [
        'matched' => 'bool',
        'result_json' => 'array',
    ];

    public function rawEmail(): BelongsTo
    {
        return $this->belongsTo(RawEmail::class);
    }

    public function parserRule(): BelongsTo
    {
        return $this->belongsTo(ParserRule::class);
    }
}
