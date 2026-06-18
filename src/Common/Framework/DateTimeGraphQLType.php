<?php declare(strict_types=1);

namespace App\Common\Framework;

use GraphQL\Language\AST\Node;

class DateTimeGraphQLType
{
    public static function serialize(\DateTimeInterface $value): string
    {
        return (new \DateTimeImmutable('@' . $value->getTimestamp(), new \DateTimeZone('UTC')))
            ->format(\DateTime::ATOM);
    }

    public static function parseValue($value): \DateTimeImmutable
    {
        return new \DateTimeImmutable($value, new \DateTimeZone('UTC'));
    }

    public static function parseLiteral(Node $valueNode): \DateTimeImmutable
    {
        return new \DateTimeImmutable($valueNode->value, new \DateTimeZone('UTC'));
    }
}
