<?php declare(strict_types=1);

namespace App\Todo\Framework;

use App\Todo\Application\TodoServiceInterface;
use App\User\Data\Entity\UserEntity;
use App\User\Data\UserEntityMapper;
use Overblog\GraphQLBundle\Definition\Argument;
use Overblog\GraphQLBundle\Definition\Resolver\AliasedInterface;
use Overblog\GraphQLBundle\Definition\Resolver\QueryInterface;
use Symfony\Component\Security\Core\Security;

class TodoGraphQLResolver implements QueryInterface, AliasedInterface
{
    private TodoServiceInterface $todoService;

    private Security $security;

    public function __construct(TodoServiceInterface $todoService, Security $security)
    {
        $this->todoService = $todoService;
        $this->security = $security;
    }

    public function findAllTodos() {
        /** @var UserEntity $userEntity */
        $userEntity = $this->security->getUser();

        $user = UserEntityMapper::map($userEntity);

        return $this->todoService->getTodosByUser($user);
    }

    public function findTodosByCompleted(Argument $args) {
        /** @var UserEntity $userEntity */
        $userEntity = $this->security->getUser();

        $user = UserEntityMapper::map($userEntity);

        return $this->todoService->getTodosByUser($user, $args['completed']);
    }

    public static function getAliases(): array
    {
        return [
            'findAllTodos' => 'all_Todos',
            'findTodosByCompleted' => 'Todo_by_completed'
        ];
    }
}
