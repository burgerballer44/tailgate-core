<?php

namespace Tailgate\Infrastructure\Persistence\ViewRepository\PDO\Specification;

class NameSpecification extends SpecificationDecorator
{
    protected $specification;

    public function __construct(SqlSpecification $specification)
    {
        $this->specification = $specification;
    }

    public function toSql(): string
    {
        return $this->specification->toSql() . " name LIKE :name ";
    }
}
