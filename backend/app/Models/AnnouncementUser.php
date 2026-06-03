<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;


#[Fillable(['announcement_id', 'user_id', 'fcm_status', 'fcm_response', 'status', 'delivered_at', 'read_at', 'clicked_at'])]
class AnnouncementUser extends Model
{
    use HasFactory;

    protected $table = 'announcement_user';

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'delivered_at' => 'datetime',
        'read_at' => 'datetime',
        'clicked_at' => 'datetime',
    ];



    // public function scopeActiveNow($query)
    // {
    //     return $query
    //         ->where(function ($q) {
    //             $q->whereNull('start_date')
    //                 ->orWhere('start_date', '<=', now());
    //         })
    //         ->where(function ($q) {
    //             $q->whereNull('end_date')
    //                 ->orWhere('end_date', '>=', now());
    //         });
    // }

    /**
     * Relationship: Announcement created by User
     */
    // public function createdBy(): BelongsTo
    // {
    //     return $this->belongsTo(User::class, 'created_by');
    // }

    /**
     * Relationship: Announcement targets many Users
     */
    // public function users(): BelongsToMany
    // {
    //     return $this->belongsToMany(User::class, 'announcement_user', 'announcement_id', 'user_id')
    //         ->withTimestamps();
    // }

    /**
     * Relationship: Announcement targets many Batches
     */
    // public function batches(): BelongsToMany
    // {
    //     return $this->belongsToMany(Batch::class, 'announcement_batch', 'announcement_id', 'batch_id')
    //         ->withTimestamps();
    // }

    /**
     * Relationship: Announcement targets many Roles
     */
    // public function roles(): BelongsToMany
    // {
    //     return $this->belongsToMany(Role::class, 'announcement_role', 'announcement_id', 'role_id')
    //         ->withTimestamps();
    // }

    /**
     * Scope: Active announcements
     */
    // public function scopeActive($query)
    // {
    //     return $query->where('status', 'published')
    //         ->where(function ($q) {
    //             $q->whereNull('start_date')->orWhere('start_date', '<=', now());
    //         })
    //         ->where(function ($q) {
    //             $q->whereNull('end_date')->orWhere('end_date', '>=', now());
    //         });
    // }

    /**
     * Scope: Get for specific user
     */
    // public function scopeForUser($query, int $userId)
    // {
    //     return $query->active()
    //         ->where(function ($q) use ($userId) {
    //             $q->whereHas('users', function ($subQ) use ($userId) {
    //                 $subQ->where('users.id', $userId);
    //             })
    //                 ->orWhereHas('batches.students', function ($subQ) use ($userId) {
    //                     $subQ->where('students.user_id', $userId);
    //                 })
    //                 ->orWhereHas('roles', function ($subQ) use ($userId) {
    //                     $subQ->whereHas('users', function ($roleQ) use ($userId) {
    //                         $roleQ->where('users.id', $userId);
    //                     });
    //                 });
    //         });
    // }
}
