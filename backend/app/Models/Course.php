<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'thumbnail',
        'code',
        'name',
        'description',
        'actual_price',
        'started_at',
        'ended_at',
        'net_price',
        'coupon_available',
        'is_renewal',
        'class_type',
        'teacher_id',
        'fee_type',
        'duration_days',
        'status',
    ];

    protected $table = 'courses';

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'fee_amount' => 'decimal:2',
        'duration_days' => 'integer',
    ];

    /**
     * Relationship: Course belongs to Teacher
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id', 'id');
    }

    public function teachers()
    {
        return $this->belongsToMany(
            User::class,
            'teachers_courses',
            'course_id',
            'teacher_id'
        );
    }

    /**
     * Relationship: Course belongs to Batch
     */
    public function batch(): BelongsTo
    {
        return $this->belongsTo(Batch::class);
    }

    /**
     * Relationship: Course has many Classes
     */
    public function classes(): HasMany
    {
        return $this->hasMany(CourseClass::class);
    }

    /**
     * Relationship: Course has many Materials
     */
    public function materials(): HasMany
    {
        return $this->hasMany(CourseMaterial::class);
    }

    public function thumbnailMedia()
    {
        return $this->belongsTo(MediaFile::class, 'thumbnail', 'id');
    }


    public function conversation() {}

    /**
     * Relationship: Course has many Payments
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Relationship: Course has many Renewals
     */
    public function renewals(): HasMany
    {
        return $this->hasMany(CourseRenewal::class);
    }

    /**
     * Relationship: Course belongs to many Students
     */
    public function students(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'admissions', 'course_id', 'student_id')
            ->withTimestamps();
    }


    public function admissions(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'admissions', 'course_id', 'student_id')
            ->withTimestamps();
    }



    /**
     * Scope: Active courses
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope: Get with relations
     */
    public function scopeWithRelations($query)
    {
        return $query->with(['teacher.user', 'batch']);
    }
}
