<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lesson extends Model
{
    use HasFactory;

    protected $fillable = [
        'section_id',
        'title',
        'description',
        'video_url',
        'duration_minutes',
        'lesson_order',
        'is_active',
        'video_metadata',
    ];

    protected $casts = [
        'duration_minutes' => 'integer',
        'lesson_order' => 'integer',
        'is_active' => 'boolean',
        'video_metadata' => 'array',
    ];

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    public function progress(): HasMany
    {
        return $this->hasMany(LessonProgress::class);
    }

    public function getProgressForUser($userId)
    {
        return $this->progress()->where('user_id', $userId)->first();
    }

    public function isCompletedByUser($userId)
    {
        $progress = $this->getProgressForUser($userId);
        return $progress ? $progress->completed : false;
    }
}
