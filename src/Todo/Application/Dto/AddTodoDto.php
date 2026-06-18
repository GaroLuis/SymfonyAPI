<?php declare(strict_types=1);

namespace App\Todo\Application\Dto;

class AddTodoDto
{
    public string $text;
    public ?string $id = null;
    public ?\DateTimeInterface $completedAt;

    public static function fromArray(array $data): self
    {
        $dto = new self();
        $dto->text = $data['text'];
        $dto->id = $data['id'] ?? null;
        $dto->completedAt = isset($data['completedAt']) ? new \DateTimeImmutable($data['completedAt'], new \DateTimeZone('UTC')) : null;

        return $dto;
    }
}