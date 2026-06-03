<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseRenewal extends Model
{
    protected $table = 'course_renewals';

    protected $fillable = [
        'student_id',
        'course_id',
        'renewal_date',
        'amount',
        'status',
        'payment_reference',
        'notes',
    ];

    protected $casts = [
        'renewal_date' => 'datetime',
        'amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relationship: Student
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Relationship: Course
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Scope: Get pending renewals
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope: Get verified renewals
     */
    public function scopeVerified($query)
    {
        return $query->where('status', 'verified');
    }

    /**
     * Scope: Get by month
     */
    public function scopeForMonth($query, $month, $year)
    {
        return $query->whereMonth('renewal_date', $month)
                     ->whereYear('renewal_date', $year);
    }

    /**
     * Mark renewal as verified
     */
    public function verify()
    {
        return $this->update(['status' => 'verified']);
    }

    /**
     * Mark renewal as rejected
     */
    public function reject()
    {
        return $this->update(['status' => 'rejected']);
    }
}
