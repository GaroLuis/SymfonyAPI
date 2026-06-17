<?php declare(strict_types=1);

namespace App\Tests\Unit\Todo;

use App\Todo\Application\Dto\AddTodoDto;
use App\Todo\Application\Dto\EditTodoDto;
use App\Todo\Application\Dto\RemoveTodoDto;
use App\Todo\Application\TodoService;
use App\Todo\Data\TodoRepository;
use App\Todo\Domain\Todo;
use App\Todo\Domain\TodoException;
use App\Todo\Domain\TodoRepositoryInterface;
use App\User\Domain\User;
use PHPUnit\Framework\TestCase;

class TodoServiceTest extends TestCase
{
    private TodoRepositoryInterface $repository;
    private TodoService $service;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(TodoRepositoryInterface::class);
        $this->service = new TodoService($this->repository);
    }

    public function testGetTodosByUserReturnsTodosFromRepository(): void
    {
        $user = new User();
        $todo = new Todo();

        $this->repository
            ->expects($this->once())
            ->method('getTodosByUser')
            ->with($user, null)
            ->willReturn([$todo]);

        $result = $this->service->getTodosByUser($user);

        $this->assertSame([$todo], $result);
    }

    public function testAddCreatesTodoAndReturnsIt(): void
    {
        $user = new User();
        $dto = AddTodoDto::fromArray(['text' => 'Test todo']);

        $this->repository
            ->expects($this->once())
            ->method('add')
            ->with($this->callback(function (Todo $todo) use ($dto, $user) {
                return $todo->getText() === $dto->text
                    && $todo->getUser() === $user
                    && $todo->getCompletedAt() === null;
            }));

        $result = $this->service->add($dto, $user);

        $this->assertInstanceOf(Todo::class, $result);
        $this->assertEquals('Test todo', $result->getText());
        $this->assertSame($user, $result->getUser());
    }

    public function testAddWithCompletedAtSetsDate(): void
    {
        $user = new User();
        $date = new \DateTimeImmutable('2025-01-15');
        $dto = AddTodoDto::fromArray(['text' => 'Test', 'completedAt' => '2025-01-15']);

        $this->repository
            ->expects($this->once())
            ->method('add')
            ->with($this->callback(function (Todo $todo) use ($date) {
                return $todo->getCompletedAt()->format('Y-m-d') === $date->format('Y-m-d');
            }));

        $this->service->add($dto, $user);
    }

    public function testRemoveDeletesTodo(): void
    {
        $user = new User();
        $user->setId('user-id');

        $todo = new Todo();
        $todo
            ->setId('todo-id')
            ->setUser($user)
        ;

        $dto = RemoveTodoDto::fromArray(['id' => 'todo-id']);

        $this->repository
            ->method('getById')
            ->with('todo-id')
            ->willReturn($todo);

        $this->repository
            ->expects($this->once())
            ->method('remove')
            ->with($todo);

        $result = $this->service->remove($dto, $user);

        $this->assertSame($todo, $result);
    }

    public function testRemoveThrowsExceptionWhenTodoNotFound(): void
    {
        $user = new User();
        $dto = RemoveTodoDto::fromArray(['id' => 'random-id']);

        $this->repository
            ->method('getById')
            ->with('random-id')
            ->willReturn(null);

        $this->expectException(TodoException::class);
        $this->expectExceptionMessage('Error: "Todo not found"');

        $this->service->remove($dto, $user);
    }

    public function testRemoveThrowsExceptionWhenTodoBelongsToDifferentUser(): void
    {
        $owner = new User();
        $owner->setId('owner-id');

        $otherUser = new User();
        $otherUser->setId('other-id');

        $todo = new Todo();
        $todo
            ->setId('todo-id')
            ->setUser($owner)
        ;

        $dto = RemoveTodoDto::fromArray(['id' => 'todo-id']);

        $this->repository
            ->method('getById')
            ->with('todo-id')
            ->willReturn($todo);

        $this->expectException(TodoException::class);

        $this->service->remove($dto, $otherUser);
    }

    public function testEditUpdatesTodoFields(): void
    {
        $user = new User();
        $user->setId('user-id');

        $todo = new Todo();
        $todo
            ->setId('todo-id')
            ->setText('Original')
            ->setUser($user);

        $dto = EditTodoDto::fromArray(['id' => 'todo-id', 'text' => 'Updated']);

        $this->repository
            ->method('getById')
            ->with('todo-id')
            ->willReturn($todo);

        $this->repository
            ->expects($this->once())
            ->method('edit')
            ->with($this->callback(function (Todo $t) {
                return $t->getText() === 'Updated';
            }));

        $result = $this->service->edit($dto, $user);

        $this->assertSame($todo, $result);
        $this->assertEquals('Updated', $result->getText());
    }

    public function testEditThrowsExceptionWhenTodoNotFound(): void
    {
        $user = new User();
        $dto = EditTodoDto::fromArray(['id' => 'random-id', 'text' => 'Updated']);

        $this->repository
            ->method('getById')
            ->with('random-id')
            ->willReturn(null);

        $this->expectException(TodoException::class);

        $this->service->edit($dto, $user);
    }

    public function testEditThrowsExceptionWhenTodoBelongsToDifferentUser(): void
    {
        $owner = new User();
        $owner->setId('owner-id');

        $otherUser = new User();
        $otherUser->setId('other-id');

        $todo = new Todo();
        $todo
            ->setId('todo-id')
            ->setUser($owner)
        ;

        $dto = EditTodoDto::fromArray(['id' => 'todo-id', 'text' => 'Updated']);

        $this->repository
            ->method('getById')
            ->with('todo-id')
            ->willReturn($todo);

        $this->expectException(TodoException::class);

        $this->service->edit($dto, $otherUser);
    }
}
