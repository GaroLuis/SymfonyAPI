<?php declare(strict_types=1);

namespace App\Todo\Application;

use App\Todo\Application\Dto\AddTodoDto;
use App\Todo\Application\Dto\EditTodoDto;
use App\Todo\Application\Dto\RemoveTodoDto;
use App\Todo\Domain\Todo;
use App\User\Domain\User;

interface TodoServiceInterface
{
    public function getTodosByUser(User $user, bool $completed = null): array;

    public function add(AddTodoDto $dto, User $user): Todo;

    public function remove(RemoveTodoDto $dto, User $user): Todo;

    public function edit(EditTodoDto $dto, User $user): Todo;
}