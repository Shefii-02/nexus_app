<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable(['name', 'email', 'password', 'agree_terms', 'phone', 'avatar', 'acc_type', 'status'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements JWTSubject
{

    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles, SoftDeletes;
    protected $guard_name = 'api';
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'agree_terms' => 'boolean',
        ];
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function teacher(): HasOne
    {
        return $this->hasOne(
            Teacher::class,
            'user_id',
            'id'
        );
    }

    public function student(): HasOne
    {
        return $this->hasOne(
            Student::class,
            'user_id',
            'id'
        );
    }

    public function staff(): HasOne
    {
        return $this->hasOne(
            Staff::class,
            'user_id',
            'id'
        );
    }

    /**
     * Relationship: Teacher has many Courses
     */
    public function courses(): HasMany
    {
        return $this->hasMany(Course::class, 'teacher_id', 'id');
    }

    public function courseTeachers()
    {
        return $this->hasMany(
            TeachersCourse::class,
            'teacher_id',
            'id'
        );
    }

    public function assignedCourses()
    {
        return $this->belongsToMany(
            Course::class,
            'teachers_courses',
            'teacher_id',
            'course_id'
        );
    }

    public function conversations()
    {
        return $this->belongsToMany(
            Conversation::class,
            'conversation_participants',
            'user_id',
            'conversation_id'
        );
    }

    public function media()
    {
        return $this->belongsTo(MediaFile::class, 'avatar');
    }

    public function getAvatarUrlAttribute()
    {
        return $this->media  ? asset('storage/' . $this->media?->file_path) : 'https://ui-avatars.com/api/?name=' . urlencode($this->name);
    }
}
