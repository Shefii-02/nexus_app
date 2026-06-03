<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['course_id', 'teacher_id', 'title', 'description', 'class_number', 'scheduled_date', 'class_link', 'record_link', 'duration_minutes', 'room_location', 'status', 'started_at', 'ended_at'])]
class CourseClass extends Model
{
    use HasFactory;

    protected $table = 'course_classes';

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'scheduled_date' => 'datetime',
        'duration_minutes' => 'integer',
    ];

    /**
     * Relationship: Class belongs to Course
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Relationship: Class belongs to Teacher
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    /**
     * Relationship: Class has links (Meet, Zoom, recordings)
     */
    public function links()
    {
        return $this->hasOne(CourseClassLink::class, 'course_class_id');
    }

    /**
     * Relationship: Class has many Materials
     */
    public function materials(): HasMany
    {
        return $this->hasMany(MediaFile::class, 'class_id');
    }

    /**
     * Scope: Upcoming classes
     */
    public function scopeUpcoming($query)
    {
        return $query->where('scheduled_date', '>', now())
            ->where('status', 'scheduled')
            ->orderBy('scheduled_date');
    }

    /**
     * Scope: Get with relations
     */
    public function scopeWithRelations($query)
    {
        return $query->with(['course', 'teacher.user', 'materials']);
    }
}
