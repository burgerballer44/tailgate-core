<?php

namespace Tailgate\Infrastructure\Persistence\Repository\Publisher;

use Buttercup\Protects\IdentifiesAggregate;
use Buttercup\Protects\RecordsEvents;
use Tailgate\Common\Event\EventPublisherInterface;
use Tailgate\Domain\Model\Team\Team;
use Tailgate\Domain\Model\Team\TeamDomainEvent;
use Tailgate\Domain\Model\Team\TeamId;
use Tailgate\Domain\Model\Team\TeamRepositoryInterface;
use Tailgate\Infrastructure\Persistence\Event\EventStoreInterface;

class TeamRepository implements TeamRepositoryInterface
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

    public function get(IdentifiesAggregate $aggregateId)
    {
        $eventStream = $this->eventStore->getAggregateHistoryFor($aggregateId);

        return Team::reconstituteFrom($eventStream);
    }

    public function add(RecordsEvents $team)
    {
        $events = $team->getRecordedEvents();
        
        foreach ($events as $event) {
            $this->domainEventPublisher->publish(TeamDomainEvent::class, $event);
        }

        $team->clearRecordedEvents();
    }

    public function nextIdentity()
    {
        return new TeamId();
    }
}
