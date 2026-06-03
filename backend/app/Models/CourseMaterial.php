<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourseMaterial extends Model
{
    use SoftDeletes;

    protected $table = 'course_materials';

    protected $fillable = [
        'course_id',
        'title',
        'description',
        'file_url',
        'material_type',
        'order',
        'status',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Relationship: Course
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Scope: Get active materials
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope: Order by sequence
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order', 'asc');
    }

    public function media()
    {
        return $this->belongsTo(MediaFile::class, 'file_url','id');
    }
}
