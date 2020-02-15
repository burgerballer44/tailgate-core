<?php

namespace Tailgate\Infrastructure\Persistence\Projection;

use Buttercup\Protects\DomainEvent;
use Buttercup\Protects\DomainEvents;

interface ProjectionInterface
{
    public function project(DomainEvents $eventStream);
    public function projectOne(DomainEvent $event);
}
