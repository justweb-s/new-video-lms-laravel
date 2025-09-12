<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Media extends Model
{
    use HasFactory;

    protected $table = 'media';

    protected $fillable = [
        'disk',
        'key',
        'filename',
        'mime_type',
        'size',
        'type', // 'image' | 'video' | other
    ];

    protected $appends = ['url'];

    public function getUrlAttribute(): string
    {
        try {
            return Storage::disk($this->disk)->url($this->key);
        } catch (\Throwable $e) {
            return $this->attributes['key'] ?? '';
        }
    }
}
