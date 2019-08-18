<?php

namespace Tailgate\Infrastructure\Persistence\Event\InMemory;

use Buttercup\Protects\AggregateHistory;
use Buttercup\Protects\DomainEvent;
use Buttercup\Protects\DomainEvents;
use Buttercup\Protects\IdentifiesAggregate;
use Tailgate\Common\Event\EventStoreInterface;

class EventStore implements EventStoreInterface
{
    private $events = [];

    public function commitOne(DomainEvent $event)
    {
        $this->events[] = $event;
    }

    public function commit(DomainEvents $events)
    {
        foreach ($events as $event) {
            $this->events[] = $event;
        }
    }

    public function getAggregateHistoryFor(IdentifiesAggregate $id)
    {
        return new AggregateHistory(
            $id,
            array_filter(
                $this->events,
                function (DomainEvent $event) use ($id) {
                    return $event->getAggregateId()->equals($id);
                }
            )
        );
    }
}
