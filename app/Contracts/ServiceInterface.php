<?php

namespace App\Contracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

interface ServiceInterface
{
    public function list(array $filters = []): LengthAwarePaginator;

    public function store(array $data): Model;

    public function update(int|string $id, array $data): Model;

    public function destroy(int|string $id): bool;
}
