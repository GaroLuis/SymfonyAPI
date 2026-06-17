<?php declare(strict_types=1);

namespace App\User\Domain;

use App\Common\Domain\CommonRepositoryInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

interface UserRepositoryInterface extends CommonRepositoryInterface
{
    public function findOneByUsername(string $username): ?User;

    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void;
}
