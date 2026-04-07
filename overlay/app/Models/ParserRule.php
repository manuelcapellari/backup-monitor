<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParserRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'vendor',
        'match_field',
        'match_pattern',
        'status',
        'priority',
        'hostname_regex',
        'job_name_regex',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'bool',
        'priority' => 'int',
    ];
}
