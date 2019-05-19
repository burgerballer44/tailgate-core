<?php

namespace Tailgate\Common\EventStore;

use Buttercup\Protects\AggregateHistory;
use Buttercup\Protects\DomainEvents;
use Buttercup\Protects\IdentifiesAggregate;

interface EventStoreInterface
{
    public function commit(DomainEvents $events);
    public function getAggregateHistoryFor(IdentifiesAggregate $id);
}