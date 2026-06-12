<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WafLog extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = [
        'domain_id',
        'ip',
        'user_agent',
        'action',
        'reason',
        'url',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    public function domain(): BelongsTo
    {
        return $this->belongsTo(Domain::class);
    }
}
