<?php declare(strict_types=1);

namespace App\Controller;

use App\Entity\Todo;
use App\Entity\User;
use App\Repository\TodoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/todos')]
class TodoController extends AbstractController
{
    private TodoRepository $todoRepository;

    public function __construct(TodoRepository $todoRepository)
    {
        $this->todoRepository = $todoRepository;
    }

    #[Route('', methods: ['POST'])]
    public function add(Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $content = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);

        $this->todoRepository->add($content['id'] ?? null, $content['text'] ?? '', $user);

        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('', methods: ['GET'])]
    public function get(): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();

        $todos = array_map(static function (Todo $todo) {
            return [
                'id' => $todo->getId(),
                'text' => $todo->getText(),
                'completed_at' => $todo->getCompletedAt() ? $todo->getCompletedAt()->format('d/m/Y') : null,
                'created_at' => $todo->getCreatedAt()->format('d/m/Y')
            ];
        },$this->todoRepository->getTodosByUser($user));

        return $this->json($todos);
    }

    #[Route('/{id}', methods: ['PATCH'])]
    public function edit(Todo $todo, Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $content = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);

        $this->todoRepository->edit($todo, $content, $user);

        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(Todo $todo): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $this->todoRepository->remove($todo, $user);

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
