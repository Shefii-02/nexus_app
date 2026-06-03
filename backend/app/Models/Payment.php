<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['student_id', 'course_id', 'amount', 'payment_date', 'payment_method', 'reference_number', 'notes', 'status'])]
class Payment extends Model
{
    use HasFactory;

    protected $table = 'payments';

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'payment_date' => 'date',
        'amount' => 'decimal:2',
    ];

    /**
     * Relationship: Payment belongs to Student
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Relationship: Payment belongs to Course
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Scope: Verified payments
     */
    public function scopeVerified($query)
    {
        return $query->where('status', 'verified');
    }

    /**
     * Scope: Pending payments
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope: Get with relations
     */
    public function scopeWithRelations($query)
    {
        return $query->with(['student.user', 'course']);
    }
}
