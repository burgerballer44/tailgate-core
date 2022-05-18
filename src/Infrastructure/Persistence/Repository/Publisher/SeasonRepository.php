<?php

namespace Tailgate\Infrastructure\Persistence\Repository\Publisher;

use Burger\Aggregate\IdentifiesAggregate;
use Burger\Aggregate\IsEventSourced;
use Burger\Aggregate\RecordsEvents;
use Burger\Event\EventPublisherInterface;
use Tailgate\Domain\Model\Season\Season;
use Tailgate\Domain\Model\Season\SeasonDomainEvent;
use Tailgate\Domain\Model\Season\SeasonId;
use Tailgate\Domain\Model\Season\SeasonRepositoryInterface;
use Tailgate\Infrastructure\Persistence\Event\EventStoreInterface;

class SeasonRepository implements SeasonRepositoryInterface
{
    private $eventStore;
    private $domainEventPublisher;

    public function __construct(
        EventStoreInterface $eventStore,
        EventPublisherInterface $domainEventPublisher
    ) {
        $this->eventStore = $eventStore;
        $this->domainEventPublisher = $domainEventPublisher;
    }

    public function get(IdentifiesAggregate $aggregateId): IsEventSourced
    {
        $eventStream = $this->eventStore->getAggregateHistoryFor($aggregateId);

        return Season::reconstituteFromEvents($eventStream);
    }

    public function add(RecordsEvents $season)
    {
        $events = $season->getRecordedEvents();

        foreach ($events as $event) {
            $this->domainEventPublisher->publish(SeasonDomainEvent::class, $event);
        };

        $season->clearRecordedEvents();
    }

    public function nextIdentity()
    {
        return new SeasonId();
    }
}
