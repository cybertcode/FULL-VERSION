<?php

namespace App\Contracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface RepositoryInterface
{
    public function all(array $columns = ['*']): Collection;

    public function paginate(int $perPage = 15, array $columns = ['*']): LengthAwarePaginator;

    public function find(int|string $id): ?Model;

    public function findOrFail(int|string $id): Model;

    public function create(array $data): Model;

    public function update(int|string $id, array $data): Model;

    public function delete(int|string $id): bool;
}
