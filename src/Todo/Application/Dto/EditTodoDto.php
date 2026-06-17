<?php declare(strict_types=1);

namespace App\Todo\Application\Dto;

class EditTodoDto
{
    public string $id;
    public string $text;
    public ?\DateTimeInterface $completedAt;

    public static function fromArray(array $data): self
    {
        $dto = new self();
        $dto->id = $data['id'];
        $dto->text = $data['text'];
        $dto->completedAt = isset($data['completed']) ? new \DateTimeImmutable() : null;

        return $dto;
    }
}