<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductAnalyticsEvent extends Model
{
    protected $fillable = [
        'project',
        'event_name',
        'feature',
        'source',
        'destination',
        'page_path',
        'session_id',
        'metadata',
        'occurred_at',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'occurred_at' => 'datetime',
        ];
    }
}
