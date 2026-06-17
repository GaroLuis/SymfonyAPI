<?php declare(strict_types=1);

namespace App\Todo\Application\Dto;

class RemoveTodoDto
{
    public string $id ;

    public static function fromArray(array $data): self
    {
        $dto = new self();
        $dto->id = $data['id'];

        return $dto;
    }
}