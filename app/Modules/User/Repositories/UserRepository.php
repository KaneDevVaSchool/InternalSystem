<?php

declare(strict_types=1);

namespace App\Modules\User\Repositories;

use App\Core\Contracts\RepositoryInterface;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

final class UserRepository implements RepositoryInterface
{
    public function find(int|string $id): ?User
    {
        return User::query()->find($id);
    }

    public function all(): Collection
    {
        return User::query()->orderBy('name')->get();
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return User::query()->orderBy('name')->paginate($perPage);
    }

    public function create(array $data): User
    {
        return User::query()->create($data);
    }

    public function update(\Illuminate\Database\Eloquent\Model $model, array $data): User
    {
        /** @var User $model */
        $model->update($data);

        return $model->fresh();
    }

    public function delete(\Illuminate\Database\Eloquent\Model $model): bool
    {
        return $model->delete();
    }

    public function findByEmail(string $email): ?User
    {
        return User::query()->where('email', $email)->first();
    }
}
