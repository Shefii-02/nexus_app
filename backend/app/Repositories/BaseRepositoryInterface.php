<?php

namespace App\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface BaseRepositoryInterface
{
    /**
     * Get paginated list of resources
     */
    public function list(int $page = 1, int $perPage = 15, array $filters = []): LengthAwarePaginator;

    /**
     * Get all resources
     */
    public function all(array $filters = []): mixed;

    /**
     * Find resource by ID
     */
    public function find(int $id): ?object;

    /**
     * Create new resource
     */
    public function create(array $data): object;

    /**
     * Update existing resource
     */
    public function update(int $id, array $data): bool;

    /**
     * Delete resource
     */
    public function delete(int $id): bool;

    /**
     * Find resource with relationships
     */
    public function findWithRelations(int $id, array $relations = []): ?object;

    /**
     * Check if resource exists
     */
    public function exists(int $id): bool;

    /**
     * Get count of resources
     */
    public function count(array $filters = []): int;
}
