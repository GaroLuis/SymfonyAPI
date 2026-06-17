<?php declare(strict_types=1);

namespace App\Todo\Framework\Controller;

use App\Common\Framework\CommonAbstractController;
use App\Todo\Application\Dto\AddTodoDto;
use App\Todo\Application\Dto\EditTodoDto;
use App\Todo\Application\Dto\RemoveTodoDto;
use App\Todo\Application\TodoServiceInterface;
use App\Todo\Domain\Todo;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/todos')]
class TodoController extends CommonAbstractController
{
    public function __construct(
        private readonly TodoServiceInterface $todoService,
    )
    {
    }

    #[Route('', methods: ['POST'])]
    public function add(Request $request): Response
    {
        $user = $this->getDomainUser();

        $dto = AddTodoDto::fromArray($request->request->all());

        $this->todoService->add($dto, $user);

        return new Response();
    }

    #[Route('', methods: ['GET'])]
    public function get(): JsonResponse
    {
        $user = $this->getDomainUser();

        $todos = $this->todoService->getTodosByUser($user);

        return $this->json(array_map(fn (Todo $todo) => $todo->toArray(), $todos));
    }

    #[Route('/{id}', methods: ['PATCH'])]
    public function edit(string $id, Request $request): Response
    {
        $user = $this->getDomainUser();
        $dto = EditTodoDto::fromArray(['id' => $id, ...$request->request->all()]);

        $this->todoService->edit($dto, $user);

        return new Response();
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(string $id): Response
    {
        $user = $this->getDomainUser();

        $dto = RemoveTodoDto::fromArray(['id' => $id]);
        $this->todoService->remove($dto, $user);

        return new Response();
    }
}
