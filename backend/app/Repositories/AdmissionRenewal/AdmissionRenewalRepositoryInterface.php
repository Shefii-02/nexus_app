<?php

namespace App\Repositories\AdmissionRenewal;

interface AdmissionRenewalRepositoryInterface
{
    public function all(array $filters = []);

    public function due();

    public function find(int $id);

    public function create(array $data);

    public function update(int $id,array $data);

    public function delete(int $id);
}
