<?php

namespace App\Repositories\Admission;

interface AdmissionRepositoryInterface
{
    public function all(array $filters = []);

    public function find(int $id);

    public function findWithRelations(int $id, array $relations = []);

    public function create(array $data);

    public function update(int $id, array $data);

    public function delete(int $id): bool;

    public function exists(int $id): bool;
}
