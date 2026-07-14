<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\Paginator;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;

abstract class BaseRepository implements BaseRepositoryInterface
{
    protected Model $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function list(int $page = 1, int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        Log::info(1);
        $query = $this->model->query();
        $query = $this->applyFilters($query, $filters);

        return $query->paginate($perPage, ['*'], 'page', $page);
    }

    public function all(array $filters = []): mixed
    {
        $query = $this->model->query();
        return $this->applyFilters($query, $filters)->get();
    }

    public function find(int $id): ?object
    {
        return $this->model->find($id);
    }

    public function create(array $data): object
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): bool
    {
        $model = $this->model->find($id);

        if (!$model) {
            return false;
        }

        return $model->update($data);
    }

    public function delete(int $id): bool
    {
        $model = $this->model->find($id);

        if (!$model) {
            return false;
        }

        return $model->delete();
    }

    public function findWithRelations(int $id, array $relations = []): ?object
    {
        return $this->model->with($relations)->find($id);
    }

    public function exists(int $id): bool
    {
        return $this->model->where('id', $id)->exists();
    }

    public function count(array $filters = []): int
    {
        $query = $this->model->query();
        return $this->applyFilters($query, $filters)->count();
    }

    /**
     * Apply filters to query
     */
    protected function applyFilters($query, array $filters)
    {
        return $query;
    }
}
