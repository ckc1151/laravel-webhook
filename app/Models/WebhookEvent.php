<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebhookEvent extends Model
{
    protected $fillable = [
        'provider',
        'event_type',
        'transaction_id',
        'payload',
        'headers',
        'raw_body',
        'file_path',
        'file_original_name',
        'file_mime_type',
        'file_size',
    ];

    protected $casts = [
        'payload' => 'array',
        'headers' => 'array',
    ];
}
