<?php

namespace App\Repositories;

interface RepositoryInterface
{
    public function findBy(string $key, mixed $value, array $columns = ['*']);

    public function findOneBy(string $key, mixed $value);

    public function create(array $attributes);

    public function update(array $attributes, int $id);

    public function paginate(int $limit = null, array $columns = ['*'], string $method = "paginate");

    public function all(array $columns = ['*']);

    public function get(array $columns = ['*']);

    public function delete(int $id);

    public function sync(int $id, string $relation, array $attributes, bool $detaching = true);

    public function detach(int $id, string $relation, int $relatedId);
}
