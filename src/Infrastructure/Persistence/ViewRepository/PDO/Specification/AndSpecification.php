<?php

namespace Tailgate\Infrastructure\Persistence\ViewRepository\PDO\Specification;

class AndSpecification extends SpecificationDecorator
{
    protected $specification;

    public function __construct(SqlSpecification $specification)
    {
        $this->specification = $specification;
    }

    public function toSql(): string
    {
        return $this->specification->toSql() . ' AND ';
    }
}
