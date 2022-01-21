<?php

namespace Tailgate\Infrastructure\Persistence\Repository\Publisher;

use Burger\Aggregate\IdentifiesAggregate;
use Burger\Aggregate\IsEventSourced;
use Burger\Aggregate\RecordsEvents;
use Burger\Event\EventPublisherInterface;
use Tailgate\Domain\Model\Group\Group;
use Tailgate\Domain\Model\Group\GroupDomainEvent;
use Tailgate\Domain\Model\Group\GroupId;
use Tailgate\Domain\Model\Group\GroupRepositoryInterface;
use Tailgate\Infrastructure\Persistence\Event\EventStoreInterface;

class GroupRepository implements GroupRepositoryInterface
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

    public function get(IdentifiesAggregate $aggregateId) : IsEventSourced
    {
        $eventStream = $this->eventStore->getAggregateHistoryFor($aggregateId);

        return Group::reconstituteFromEvents($eventStream);
    }

    public function add(RecordsEvents $group)
    {
        $events = $group->getRecordedEvents();
        
        foreach ($events as $event) {
            $this->domainEventPublisher->publish(GroupDomainEvent::class, $event);
        }

        $group->clearRecordedEvents();
    }

    public function nextIdentity()
    {
        return new GroupId();
    }
}
