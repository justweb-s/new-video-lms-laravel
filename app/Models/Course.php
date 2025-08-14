<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'image_url',
        'price',
        'is_active',
        'duration_weeks',
        'prerequisites',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
        'duration_weeks' => 'integer',
    ];

    public function sections(): HasMany
    {
        return $this->hasMany(Section::class)->orderBy('section_order');
    }

    public function workoutCard(): HasOne
    {
        return $this->hasOne(WorkoutCard::class);
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'enrollments')
                    ->withPivot(['enrolled_at', 'expires_at', 'is_active', 'progress_percentage'])
                    ->withTimestamps();
    }

    public function lessons(): HasManyThrough
    {
        return $this->hasManyThrough(
            Lesson::class,
            Section::class,
            'course_id',   // Foreign key on sections table referencing courses.id
            'section_id',  // Foreign key on lessons table referencing sections.id
            'id',          // Local key on courses
            'id'           // Local key on sections
        );
    }

    public function getTotalLessonsAttribute()
    {
        return $this->lessons()->count();
    }

    public function getActiveStudentsCountAttribute()
    {
        return $this->enrollments()->where('is_active', true)->count();
    }
}
