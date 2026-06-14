<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentAnalysis extends Model
{
    protected $fillable = [
        'document_id',
        'summary',
        'key_points',
        'inconsistencies',
        'openai_request_payload',
        'openai_response',
        'analyzed_by',
        'analyzed_at',
    ];

    protected function casts(): array
    {
        return [
            'key_points' => 'array',
            'inconsistencies' => 'array',
            'openai_request_payload' => 'array',
            'openai_response' => 'array',
            'analyzed_at' => 'datetime',
        ];
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    public function analyzer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'analyzed_by');
    }
}
