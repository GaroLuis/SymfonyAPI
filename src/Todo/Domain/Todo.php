<?php declare(strict_types=1);

namespace App\Todo\Domain;

use App\User\Domain\User;
use Symfony\Component\Uid\Uuid;

class Todo
{
    private string $id;

    private string $text;

    private ?\DateTimeInterface $completedAt = null;

    private \DateTimeInterface $createdAt;

    private User $user;

    public function __construct()
    {
        $this->id = (string) Uuid::v4();
        $this->createdAt = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(string $id): self
    {
        $this->id = $id;

        return $this;
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

    public function setCompletedAt(?\DateTimeInterface $completedAt): self
    {
        $this->completedAt = $completedAt;

        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'text' => $this->getText(),
            'completed_at' => $this->getCompletedAt()?->format(\DateTime::ATOM),
            'created_at' => $this->getCreatedAt()->format(\DateTime::ATOM)
        ];
    }
}
