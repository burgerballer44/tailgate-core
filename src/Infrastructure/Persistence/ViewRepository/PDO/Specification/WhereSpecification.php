<?php

namespace Tailgate\Infrastructure\Persistence\ViewRepository\PDO\Specification;

use Tailgate\Infrastructure\Persistence\ViewRepository\PDO\Specification\SqlSpecification;

class WhereSpecification implements SqlSpecification
{
    public function toSql(): string
    {
        return ' WHERE 1=1 ';
    }
}