<?php

namespace Tailgate\Infrastructure\Persistence\Event\InMemory;

use Burger\Aggregate\AggregateHistory;
use Burger\Aggregate\DomainEvent;
use Burger\Aggregate\DomainEvents;
use Burger\Aggregate\IdentifiesAggregate;
use Tailgate\Infrastructure\Persistence\Event\EventStoreInterface;

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
