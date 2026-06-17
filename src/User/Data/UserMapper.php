<?php declare(strict_types=1);

namespace App\User\Data;

use App\User\Data\Entity\UserEntity;
use App\User\Domain\User;

class UserMapper
{
    static function map(UserEntity $entity): User
    {
        $user = new User();

        $user
            ->setId($entity->getId())
            ->setUsername($entity->getUsername())
            ->setRoles($entity->getRoles());

        return $user;
    }
}