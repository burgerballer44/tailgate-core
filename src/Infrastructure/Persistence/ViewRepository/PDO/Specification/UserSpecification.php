<?php

namespace Tailgate\Infrastructure\Persistence\ViewRepository\PDO\Specification;

use Tailgate\Infrastructure\Persistence\ViewRepository\PDO\Specification\SpecificationDecorator;
use Tailgate\Infrastructure\Persistence\ViewRepository\PDO\Specification\SqlSpecification;

class UserSpecification extends SpecificationDecorator
{
    protected $specification;

    public function __construct(SqlSpecification $specification)
    {
        $this->specification = $specification;
    }

    public function toSql(): string
    {
        return $this->specification->toSql() . ' user_id = :user_id ';
    }
}
