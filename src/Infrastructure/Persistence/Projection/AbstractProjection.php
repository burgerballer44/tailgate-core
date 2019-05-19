<?php

namespace Tailgate\Infrastructure\Persistence\Projection;

use Buttercup\Protects\DomainEvents;
use Tailgate\Common\Projection\ProjectionInterface;
use Verraes\ClassFunctions\ClassFunctions;

abstract class AbstractProjection implements ProjectionInterface
{
    public function project(DomainEvents $eventStream)
    {
        foreach ($eventStream as $event) {
            $projectMethod = 'project' . ClassFunctions::short($event);
            $this->$projectMethod($event);
        }
    }
}
