<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\Todo;
use App\Entity\User;
use App\Exception\TodoException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Todo>
 *
 * @method Todo|null find($id, $lockMode = null, $lockVersion = null)
 * @method Todo|null findOneBy(array $criteria, array $orderBy = null)
 * @method Todo[]    findAll()
 * @method Todo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TodoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Todo::class);
    }

    public function getTodosByUser(User $user, bool $completed = null)
    {
        $qb = $this->createQueryBuilder('todos')
            ->andWhere('todos.user = :user')
            ->setParameter('user', $user);

        if (null !== $completed) {
            if ($completed) {
                $qb->andWhere('todos.completedAt IS NOT NULL');
            } else {
                $qb->andWhere('todos.completedAt IS NULL');
            }
        }

        return $qb->getQuery()->getResult();
    }

    public function add(?string $id, string $text, User $user): void
    {
        $todo = new Todo($id, $text, $user);

        $this->getEntityManager()->persist($todo);
        $this->getEntityManager()->flush();
    }

    public function remove(Todo $todo, User $user): void
    {
        if ($todo->getUser() !== $user) {
            throw TodoException::todoNotFound();
        }

        $this->getEntityManager()->remove($todo);
        $this->getEntityManager()->flush();
    }

    public function edit(Todo $todo, array $data, User $user): void
    {
        if ($todo->getUser() !== $user) {
            throw TodoException::todoNotFound();
        }

        if (array_key_exists('completed', $data)) {
            $todo->setCompletedAt($data['completed'] === true ? new \DateTimeImmutable() : null);
        }

        if (array_key_exists('text', $data)) {
            $todo->setText($data['text']);
        }

        $this->getEntityManager()->flush();
    }
}
