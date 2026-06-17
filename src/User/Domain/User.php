<?php declare(strict_types=1);

namespace App\User\Domain;

use Symfony\Component\Uid\Uuid;

class User
{
    private ?string $id;

    private string $username;

    private array $roles = [];

    private $password;

    private array $todos;

    public function __construct()
    {
        $this->id = (string) Uuid::v4();
    }

    public function setId(?string $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function setPassword($password): self
    {
        $this->password = $password;

        return $this;
    }

    public function setTodos(array $todos): self
    {
        $this->todos = $todos;

        return $this;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getTodos(): array
    {
        return $this->todos;
    }
}
