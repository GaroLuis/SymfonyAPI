<?php declare(strict_types=1);

namespace App\Todo\Domain;

use App\Common\Domain\DomainException;

class TodoException extends DomainException
{
    public static function notFound()
    {
        return self::create('Todo not found', 404);
    }
}
