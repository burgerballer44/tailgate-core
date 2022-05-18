<?php

namespace Tailgate\Infrastructure\Persistence\Event;

use Burger\Aggregate\DomainEvent;
use Burger\Aggregate\DomainEvents;
use Burger\Aggregate\IdentifiesAggregate;

interface EventStoreInterface
{
    public function commitOne(DomainEvent $event);

    public function commit(DomainEvents $events);

    public function getAggregateHistoryFor(IdentifiesAggregate $id);
}
