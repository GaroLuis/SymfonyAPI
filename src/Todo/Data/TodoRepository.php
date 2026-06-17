<?php declare(strict_types=1);

namespace App\Todo\Data;

use App\Todo\Data\Entity\TodoEntity;
use App\Todo\Domain\Todo;
use App\Todo\Domain\TodoRepositoryInterface;
use App\User\Data\UserMapper;
use App\User\Domain\User;
use App\User\Domain\UserRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class TodoRepository extends ServiceEntityRepository implements TodoRepositoryInterface
{
    public function __construct(
        ManagerRegistry $registry,
        private readonly UserRepositoryInterface $userRepository,
    )
    {
        parent::__construct($registry, TodoEntity::class);
    }

    public function getById(string $id): ?Todo
    {
        $qb = $this->createQueryBuilder('todos')
            ->addSelect('users')
            ->leftJoin('todos.user', 'users')
            ->where('todos.id = :id')
            ->setParameter('id', $id)
            ->setMaxResults(1)
        ;

        /** @var TodoEntity $entity */
        $entity = $qb->getQuery()->getOneOrNullResult();

        if (null === $entity) {
            return null;
        }

        $todo = TodoEntityMapper::map($entity);
        $user = UserMapper::map($entity->getUser());

        $todo->setUser($user);

        return $todo;
    }

    /** @return Todo[] */
    public function getTodosByUser(User $user, bool $completed = null): array
    {
        $qb = $this->createQueryBuilder('todos')
            ->addSelect('users')
            ->leftJoin('todos.user', 'users')
            ->andWhere('users.id = :user')
            ->setParameter('user', $user->getId());

        if (null !== $completed) {
            if ($completed) {
                $qb->andWhere('todos.completedAt IS NOT NULL');
            } else {
                $qb->andWhere('todos.completedAt IS NULL');
            }
        }

        /** @var TodoEntity[] $entities */
        $entities =  $qb->getQuery()->getResult();

        return array_map(function ($entity) {
            $todo = TodoEntityMapper::map($entity);
            $user = UserMapper::map($entity->getUser());

            $todo->setUser($user);

            return $todo;
        }, $entities);
    }

    public function add(Todo $todo): void
    {
        $entity = TodoEntity::create($todo);

        $user = $this->userRepository->find($todo->getUser()->getId());
        $entity->setUser($user);

        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();
    }

    public function remove(Todo $todo): void
    {
        $entity = $this->find($todo->getId());

        $this->getEntityManager()->remove($entity);
        $this->getEntityManager()->flush();
    }

    public function edit(Todo $todo): void
    {
        /** @var Todo $entity */
        $entity = $this->find($todo->getId());

        $entity->setText($todo->getText());
        $entity->setCompletedAt($todo->getCompletedAt());

        $this->getEntityManager()->flush();
    }
}
