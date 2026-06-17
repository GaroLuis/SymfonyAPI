<?php declare(strict_types=1);

namespace App\Todo\Domain;

use App\Common\Domain\CommonRepositoryInterface;
use App\User\Domain\User;

interface TodoRepositoryInterface extends CommonRepositoryInterface
{
    public function getTodosByUser(User $user, bool $completed = null): array;

    public function add(Todo $todo): void;

    public function remove(Todo $todo): void;

    public function edit(Todo $todo): void;
}
