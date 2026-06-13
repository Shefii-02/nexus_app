<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['user_id', 'roll_number', 'phone', 'address', 'guardian_name', 'guardian_phone', 'status'])]
class Student extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'students';

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relationship: Student belongs to User
     */

    public function user(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'user_id',
            'id'
        );
    }


    /**
     * Relationship: Student belongs to many Batches (via pivot)
     */
    public function batches(): BelongsToMany
    {
        return $this->belongsToMany(Batch::class, 'student_batch', 'student_id', 'batch_id')
            ->withTimestamps()
            ->withPivot('admitted_at', 'graduated_at', 'status');
    }

    /**
     * Relationship: Student has many Payments
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Relationship: Student has many Course Renewals (for monthly courses)
     */
    public function renewals(): HasMany
    {
        return $this->hasMany(CourseRenewal::class);
    }

    /**
     * Relationship: Student belongs to many Courses
     */
    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'course_students', 'student_id', 'course_id')
            ->withTimestamps();
    }

    /**
     * Scope: Active students
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope: Get active students
     */
    public function scopeActiveOnly($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope: Get with user and batches
     */
    public function scopeWithRelations($query)
    {
        return $query->with(['user', 'batches']);
    }

    /**
     * Get primary batch (first enrolled batch)
     */
    public function getPrimaryBatch()
    {
        return $this->batches()->orderBy('student_batch.admitted_at')->first();
    }
}
