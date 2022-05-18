<?php

namespace Tailgate\Infrastructure\Persistence\ViewRepository\PDO\Specification;

abstract class SpecificationDecorator implements SqlSpecification
{
    protected $specification;

    public function __construct(SqlSpecification $specification)
    {
        $this->specification = $specification;
    }
}
