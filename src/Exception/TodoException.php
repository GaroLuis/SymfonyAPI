<?php declare(strict_types=1);

namespace App\Exception;

class TodoException extends DomainException
{
    public static function todoNotFound()
    {
        return self::create('Todo not found', 404);
    }
}
