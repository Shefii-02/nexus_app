<?php

namespace App\Repositories\Announcement;

use App\Models\Announcement;
use App\Repositories\BaseRepository;

class AnnouncementRepository extends BaseRepository
{
    public function __construct(\App\Models\Announcement $model)
    {
        parent::__construct($model);
    }

    public function listForUser(int $userId)
    {
        return $this->model
            ->activeNow() // 👈 here
            ->where('status', 'active')

            // ✅ ADD HERE
            ->where(function ($q) {
                $q->whereNull('start_date')
                    ->orWhere('start_date', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('end_date')
                    ->orWhere('end_date', '>=', now());
            })

            ->where(function ($q) use ($userId) {

                $q->where('target_type', 'all')

                    ->orWhereHas('users', fn($q) => $q->where('user_id', $userId))

                    ->orWhereHas('roles', function ($q) {
                        $q->whereIn('role_id', auth()->user()->roles->pluck('id'));
                    })

                    ->orWhereHas('batches', function ($q) use ($userId) {
                        $batchIds = \App\Models\Student::where('user_id', $userId)
                            ->pluck('batch_id');

                        $q->whereIn('batch_id', $batchIds);
                    });
            })
            ->latest()
            ->paginate(15);
    }
}
