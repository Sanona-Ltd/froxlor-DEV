<?php

namespace App\Models;

use App\Enums\Webserver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Domain extends Model
{
    protected $table = 'hosting_domains';

    protected $fillable = [
        'user_id',
        'name',
        'document_root',
        'webserver',
        'php_version',
        'ssl_enabled',
        'waf_enabled',
    ];

    protected function casts(): array
    {
        return [
            'webserver'   => Webserver::class,
            'ssl_enabled' => 'boolean',
            'waf_enabled' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function resolvedDocumentRoot(): string
    {
        return str_replace('{domain}', $this->name, $this->document_root);
    }
}
