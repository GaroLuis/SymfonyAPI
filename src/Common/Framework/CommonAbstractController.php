<?php declare(strict_types=1);

namespace App\Common\Framework;

use App\User\Data\Entity\UserEntity;
use App\User\Data\UserEntityMapper;
use App\User\Domain\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

abstract class CommonAbstractController extends AbstractController
{
    public function getDomainUser(): ?User
    {
        /** @var UserEntity $user */
        $user = $this->getUser();

        return UserEntityMapper::map($user);
    }
}