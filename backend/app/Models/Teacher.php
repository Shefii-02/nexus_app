<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;



#[Fillable(['user_id', 'qualification', 'subject', 'experience_years', 'phone', 'address', 'status'])]
class Teacher extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'teachers';

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'experience_years' => 'integer',
    ];

    /**
     * Relationship: Teacher belongs to User
     */
    // public function user(): BelongsTo
    // {
    //     return $this->belongsTo(User::class);
    // }
    public function user(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'user_id',
            'id'
        );
    }

    /**
     * Relationship: Teacher has many Courses
     */
    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }

    /**
     * Relationship: Teacher has many Classes
     */
    public function classes(): HasMany
    {
        return $this->hasMany(CourseClass::class, 'teacher_id');
    }

    /**
     * Scope: Active teachers
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope: Get teacher with user details
     */
    public function scopeWithUser($query)
    {
        return $query->with(['user']);
    }
}
