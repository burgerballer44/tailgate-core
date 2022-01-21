<?php

namespace Tailgate\Infrastructure\Persistence\Projection;

use Burger\Aggregate\DomainEvent;
use Burger\Aggregate\DomainEvents;
use Tailgate\Infrastructure\Persistence\Projection\ProjectionInterface;
use Verraes\ClassFunctions\ClassFunctions;

abstract class AbstractProjection implements ProjectionInterface
{
    const DATE_FORMAT = 'Y-m-d H:i:s';

    public function project(DomainEvents $eventStream)
    {
        foreach ($eventStream as $event) {
            $projectMethod = 'project' . ClassFunctions::short($event);
            $this->$projectMethod($event);
        }
    }

    public function projectOne(DomainEvent $event)
    {
        $projectMethod = 'project' . ClassFunctions::short($event);
        $this->$projectMethod($event);
    }
}
