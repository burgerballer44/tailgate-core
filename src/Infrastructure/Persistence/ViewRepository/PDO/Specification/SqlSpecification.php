<?php

namespace Tailgate\Infrastructure\Persistence\ViewRepository\PDO\Specification;

interface SqlSpecification
{
    public function toSql() : string;
}