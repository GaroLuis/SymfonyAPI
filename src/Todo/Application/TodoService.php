<?php declare(strict_types=1);

namespace App\Todo\Application;

use App\Todo\Application\Dto\AddTodoDto;
use App\Todo\Application\Dto\EditTodoDto;
use App\Todo\Application\Dto\RemoveTodoDto;
use App\Todo\Domain\Todo;
use App\Todo\Domain\TodoException;
use App\Todo\Domain\TodoRepositoryInterface;
use App\User\Domain\User;

class TodoService implements TodoServiceInterface
{
    public function __construct(private readonly TodoRepositoryInterface $TodoRepository)
    {
    }

    public function getTodosByUser(User $user, bool $completed = null): array
    {
        return $this->TodoRepository->getTodosByUser($user, $completed);
    }

    public function add(AddTodoDto $dto, User $user): Todo
    {
        $todo = new Todo();

        $todo
            ->setId($dto->id)
            ->setText($dto->text)
            ->setCompletedAt($dto->completedAt)
            ->setUser($user)
        ;

        $this->TodoRepository->add($todo);

        return $todo;
    }

    public function remove(RemoveTodoDto $dto, User $user): Todo
    {
        $todo = $this->TodoRepository->getById($dto->id);

        if ($todo?->getUser()?->getId() !== $user->getId()) {
            throw TodoException::notFound();
        }

        $this->TodoRepository->remove($todo);

        return $todo;
    }

    public function edit(EditTodoDto $dto, User $user): Todo
    {
        $todo = $this->TodoRepository->getById($dto->id);

        if ($todo?->getUser()?->getId() !== $user->getId()) {
            throw TodoException::notFound();
        }

        $todo
            ->setText($dto->text)
            ->setCompletedAt($dto->completedAt)
        ;

        $this->TodoRepository->edit($todo);

        return $todo;
    }
}