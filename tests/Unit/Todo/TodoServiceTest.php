<?php declare(strict_types=1);

namespace App\Tests\Unit\Todo;

use App\Todo\Application\Dto\AddTodoDto;
use App\Todo\Application\Dto\EditTodoDto;
use App\Todo\Application\Dto\RemoveTodoDto;
use App\Todo\Application\TodoService;
use App\Todo\Domain\Todo;
use App\Todo\Domain\TodoException;
use App\Todo\Domain\TodoRepositoryInterface;
use App\User\Domain\User;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

class TodoServiceTest extends TestCase
{
    use ProphecyTrait;

    private ObjectProphecy|TodoRepositoryInterface $repository;
    private TodoService $service;

    protected function setUp(): void
    {
        $this->repository = $this->prophesize(TodoRepositoryInterface::class);
        $this->service = new TodoService($this->repository->reveal());
    }

    public function testGetTodosByUser(): void
    {
        $user = new User();
        $todo = new Todo();

        $this->repository
            ->getTodosByUser($user, null)
            ->shouldBeCalledOnce()
            ->willReturn([$todo])
        ;

        $result = $this->service->getTodosByUser($user);

        $this->assertSame([$todo], $result);
    }

    public function testAddCreatesTodoAndReturnsIt(): void
    {
        $user = new User();
        $dto = AddTodoDto::fromArray(['text' => 'Test todo']);

        $this->repository
            ->add(Argument::that(function (Todo $todo) use ($user, $dto) {
                return $todo->getText() === $dto->text
                    && $todo->getUser() === $user
                    && $todo->getCompletedAt() === null;
            }))
            ->shouldBeCalledOnce()
        ;

        $result = $this->service->add($dto, $user);

        $this->assertInstanceOf(Todo::class, $result);
        $this->assertEquals('Test todo', $result->getText());
        $this->assertSame($user, $result->getUser());
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
            ->getById('todo-id')
            ->shouldBeCalledOnce()
            ->willReturn($todo)
        ;

        $this->repository
            ->remove($todo)
            ->shouldBeCalledOnce()
        ;

        $result = $this->service->remove($dto, $user);

        $this->assertSame($todo, $result);
    }

    public function testRemoveThrowsExceptionWhenTodoNotFound(): void
    {
        $user = new User();
        $dto = RemoveTodoDto::fromArray(['id' => 'random-id']);

        $this->repository
            ->getById('random-id')
            ->shouldBeCalledOnce()
            ->willReturn(null)
        ;

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
            ->getById('todo-id')
            ->shouldBeCalledOnce()
            ->willReturn($todo)
        ;

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
            ->getById('todo-id')
            ->shouldBeCalledOnce()
            ->willReturn($todo)
        ;

        $this->repository
            ->edit(Argument::that(function (Todo $t) {
                return $t->getText() === 'Updated';
            }))
            ->shouldBeCalledOnce()
        ;

        $result = $this->service->edit($dto, $user);

        $this->assertSame($todo, $result);
        $this->assertEquals('Updated', $result->getText());
    }

    public function testEditThrowsExceptionWhenTodoNotFound(): void
    {
        $user = new User();
        $dto = EditTodoDto::fromArray(['id' => 'random-id', 'text' => 'Updated']);

        $this->repository
            ->getById('random-id')
            ->shouldBeCalledOnce()
            ->willReturn(null)
        ;

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
            ->getById('todo-id')
            ->shouldBeCalledOnce()
            ->willReturn($todo)
        ;

        $this->expectException(TodoException::class);

        $this->service->edit($dto, $otherUser);
    }
}
