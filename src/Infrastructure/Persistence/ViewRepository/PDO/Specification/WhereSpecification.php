<?php

namespace Tailgate\Infrastructure\Persistence\ViewRepository\PDO\Specification;

class WhereSpecification implements SqlSpecification
{
    public function toSql(): string
    {
        return ' WHERE 1=1 ';
    }
}
