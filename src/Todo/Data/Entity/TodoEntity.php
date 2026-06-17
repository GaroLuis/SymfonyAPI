<?php declare(strict_types=1);

namespace App\Todo\Data\Entity;

use App\Todo\Data\TodoRepository;
use App\Todo\Domain\Todo;
use App\User\Data\Entity\UserEntity;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TodoRepository::class)]
#[ORM\Table(name: 'todos')]
class TodoEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'NONE')]
    #[ORM\Column(name: 'id', type: 'guid')]
    private string $id;

    #[ORM\Column(type: 'string', length: 255)]
    private string $text;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeInterface $completedAt = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeInterface $createdAt;

    #[ORM\ManyToOne(targetEntity: UserEntity::class, inversedBy: 'todos')]
    #[ORM\JoinColumn(name: 'user_id', nullable: false)]
    private UserEntity $user;

    private function __construct()
    {
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function getCompletedAt(): ?\DateTimeInterface
    {
        return $this->completedAt;
    }

    public function setCompletedAt(?\DateTimeImmutable $completedAt): self
    {
        $this->completedAt = $completedAt;

        return $this;
    }

    public function getUser(): ?UserEntity
    {
        return $this->user;
    }

    public function setUser(?UserEntity $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public static function create(Todo $Todo): self
    {
        $entity = new self();

        $entity->id = $Todo->getId();
        $entity->text = $Todo->getText();
        $entity->completedAt = $Todo->getCompletedAt();
        $entity->createdAt = $Todo->getCreatedAt();

        return $entity;
    }
}
