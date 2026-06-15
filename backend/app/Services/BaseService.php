<?php

namespace App\Services;

use App\Repositories\BaseRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

abstract class BaseService
{
    protected BaseRepositoryInterface $repository;

    public function __construct(BaseRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function list(int $page = 1, int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        return $this->repository->list($page, $perPage, $filters);
    }

    public function all(array $filters = []): mixed
    {
        return $this->repository->all($filters);
    }

    public function find(int $id): ?object
    {
        return $this->repository->find($id);
    }

    // public function findWithRelations(int $id, array $relations = []): ?object
    // {
    //     return $this->repository->findWithRelations($id, $relations);
    // }

    public function exists(int $id): bool
    {
        return $this->repository->exists($id);
    }

    public function count(array $filters = []): int
    {
        return $this->repository->count($filters);
    }

    /**
     * Format response data
     */
    protected function formatResponse(bool $success, string $message, mixed $data = null, int $statusCode = 200): array
    {
        return [
            'status' => $success,
            'message' => $message,
            'data' => $data,
            'code' => $statusCode,
        ];
    }
}
