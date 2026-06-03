<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Permission\Traits\HasRoles;

use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    use HasRoles;
    protected $fillable = ['name','guard_name'];
    protected $hidden = ['guard_name'];
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];


    /**
     * Get all announcements with this role
     */
    public function announcements(): BelongsToMany
    {
        return $this->belongsToMany(Announcement::class, 'announcement_role', 'role_id', 'announcement_id');
    }
}
