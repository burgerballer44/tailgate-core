<?php

namespace Tailgate\Infrastructure\Persistence\Event;

use Buttercup\Protects\AggregateHistory;
use Buttercup\Protects\DomainEvent;
use Buttercup\Protects\DomainEvents;
use Buttercup\Protects\IdentifiesAggregate;

interface EventStoreInterface
{
    public function commitOne(DomainEvent $event);
    public function commit(DomainEvents $events);
    public function getAggregateHistoryFor(IdentifiesAggregate $id);
}
