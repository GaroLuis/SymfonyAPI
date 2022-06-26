<?php declare(strict_types=1);

namespace App\Graphql\Resolver;

use App\Entity\User;
use App\Repository\TodoRepository;
use Overblog\GraphQLBundle\Definition\Argument;
use Overblog\GraphQLBundle\Definition\Resolver\AliasedInterface;
use Overblog\GraphQLBundle\Definition\Resolver\QueryInterface;
use Symfony\Component\Security\Core\Security;

class TodoResolver implements QueryInterface, AliasedInterface
{
    private TodoRepository $todoRepository;

    private Security $security;

    public function __construct(TodoRepository $todoRepository, Security $security)
    {
        $this->todoRepository = $todoRepository;
        $this->security = $security;
    }

    public function findAllTodos() {
        /** @var User $user */
        $user = $this->security->getUser();

        return $this->todoRepository->getTodosByUser($user);
    }

    public function findTodosByCompleted(Argument $args) {
        /** @var User $user */
        $user = $this->security->getUser();

        return $this->todoRepository->getTodosByUser($user, $args['completed']);
    }

    public static function getAliases(): array
    {
        return [
            'findAllTodos' => 'all_todos',
            'findTodosByCompleted' => 'todo_by_completed'
        ];
    }
}
