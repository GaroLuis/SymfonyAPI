<?php declare(strict_types=1);

namespace App\Todo\Data;

use App\Todo\Data\Entity\TodoEntity;
use App\Todo\Domain\Todo;

class TodoEntityMapper
{
    static function map(TodoEntity $entity): Todo
    {
        $todo = new Todo();

        $todo
            ->setText($entity->getText())
            ->setCreatedAt($entity->getCreatedAt())
            ->setCompletedAt($entity->getCompletedAt())
            ->setId($entity->getId())
        ;

        return $todo;
    }
}