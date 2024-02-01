<?php

namespace App\Repositories\Eloquent;

use App\Exceptions\RepositoryException;
use App\Repositories\RepositoryInterface;
use Illuminate\Container\Container as Application;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

abstract class BaseRepository implements RepositoryInterface
{
    /**
     * @throws RepositoryException
     * @throws BindingResolutionException
     */
    public function __construct(private readonly Application $app)
    {
        $this->makeModel();
    }

    /**
     * @return Model
     * @throws BindingResolutionException
     * @throws RepositoryException
     */
    protected function makeModel(): Model
    {
        $model = $this->app->make($this->model());

        if (!$model instanceof Model) {
            throw new RepositoryException(
                "The class {$this->model()} must be an instance of Illuminate\\Database\\Eloquent\\Model"
            );
        }

        return $model;
    }

    /**
     * @return void
     * @throws BindingResolutionException
     * @throws RepositoryException
     */
    private function resetModel(): void
    {
        $this->makeModel();
    }

    /**
     * @param string $key
     * @param mixed $value
     * @param array<int, string> $columns
     * @return Collection
     * @throws BindingResolutionException
     * @throws RepositoryException
     */
    public function findBy(string $key, mixed $value, array $columns = ['*']): Collection
    {
        return $this->makeModel()->query()->where($key, $value)->get($columns);
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return mixed
     * @throws BindingResolutionException
     * @throws RepositoryException
     */
    public function findOneBy(string $key, mixed $value)
    {
        return $this->makeModel()->query()->where($key, $value)->first();
    }

    /**
     * @param array<string, mixed> $attributes
     * @throws BindingResolutionException
     * @throws RepositoryException
     */
    public function create(array $attributes)
    {
        $model = $this->makeModel()->newInstance($attributes);
        $model->save();

        $this->resetModel();

        return $model;
    }

    /**
     * @param array<string, mixed> $attributes
     * @param int $id
     * @throws BindingResolutionException
     * @throws RepositoryException
     */
    public function update(array $attributes, int $id)
    {
        $model = $this->makeModel()->findOrFail($id);
        $model->fill($attributes);
        $model->save();

        $this->resetModel();

        return $model;
    }

    /**
     * @param int|null $limit
     * @param array<int, string> $columns
     * @param string $method
     * @return Paginator
     * @throws BindingResolutionException
     * @throws RepositoryException
     */
    public function paginate(int $limit = null, array $columns = ['*'], string $method = "paginate"): Paginator
    {
        $limit = is_null($limit) ? config('pagination.limit', 15) : $limit;

        $results = $this->makeModel()->{$method}($limit, $columns);
        $results->appends($this->app->make('request')->query());

        $this->resetModel();

        return $results;
    }

    /**
     * @param array<int, string> $columns
     * @return Collection|array<int, array<string, mixed>>
     * @throws BindingResolutionException
     * @throws RepositoryException
     */
    public function all(array $columns = ['*']): Collection|array
    {
        $results = $this->makeModel()->all($columns);

        $this->resetModel();

        return $results;
    }

    /**
     * @param array<int, string> $columns
     * @return Collection|array<int, array<string, mixed>>
     * @throws BindingResolutionException
     * @throws RepositoryException
     */
    public function get(array $columns = ['*']): Collection|array
    {
        return $this->all($columns);
    }

    /**
     * @param int $id
     * @return bool|null
     * @throws BindingResolutionException
     * @throws RepositoryException
     */
    public function delete(int $id): bool|null
    {
        return $this->makeModel()->findOrFail($id)->delete();
    }

    /**
     * @param int $id
     * @param string $relation
     * @param array<string, mixed> $attributes
     * @param bool $detaching
     * @return mixed
     * @throws BindingResolutionException
     * @throws RepositoryException
     */
    public function sync(int $id, string $relation, array $attributes, bool $detaching = true): mixed
    {
        return $this->makeModel()->find($id)->{$relation}()->sync($attributes, $detaching);
    }

    /**
     * @param int $id
     * @param string $relation
     * @param int $relatedId
     * @return int
     * @throws BindingResolutionException
     * @throws RepositoryException
     */
    public function detach(int $id, string $relation, int $relatedId): int
    {
        return $this->makeModel()->find($id)->{$relation}()->detach($relatedId);
    }

    abstract public function model(): string;
}
