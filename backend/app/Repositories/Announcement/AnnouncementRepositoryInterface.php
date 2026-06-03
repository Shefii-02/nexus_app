<?php

namespace App\Repositories\Announcement;

use App\Repositories\BaseRepositoryInterface;

interface AnnouncementRepositoryInterface extends BaseRepositoryInterface
{
    public function getPublished();

    public function getForUser(int $userId);

    public function getByStatus(string $status);

    public function getActive();
}
