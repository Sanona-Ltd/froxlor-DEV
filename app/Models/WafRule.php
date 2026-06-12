<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WafRule extends Model
{
    protected $fillable = [
        'type',
        'value',
        'action',
        'active',
        'note',
    ];

    protected function casts(): array
    {
        return [
            'active' => 'boolean',
        ];
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
}
