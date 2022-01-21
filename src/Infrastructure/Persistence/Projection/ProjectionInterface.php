<?php

namespace Tailgate\Infrastructure\Persistence\Projection;

use Burger\Aggregate\DomainEvent;
use Burger\Aggregate\DomainEvents;

interface ProjectionInterface
{
    public function project(DomainEvents $eventStream);
    public function projectOne(DomainEvent $event);
}
