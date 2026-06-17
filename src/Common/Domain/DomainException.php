<?php declare(strict_types=1);

namespace App\Common\Domain;

abstract class DomainException extends \LogicException
{
    protected static function create(string $message, int $code = 500): self
    {
        return new static(sprintf('Error: "%s"', $message), $code);
    }
}
