<?php

namespace Tailgate\Infrastructure\Persistence\Repository\Manual;

use Burger\Aggregate\IdentifiesAggregate;
use Burger\Aggregate\RecordsEvents;
use Tailgate\Domain\Model\Group\Group;
use Tailgate\Domain\Model\Group\GroupId;
use Tailgate\Domain\Model\Group\GroupRepositoryInterface;
use Tailgate\Infrastructure\Persistence\Event\EventStoreInterface;
use Tailgate\Infrastructure\Persistence\Projection\GroupProjectionInterface;

class GroupRepository implements GroupRepositoryInterface
{
    private $eventStore;
    private $groupProjection;

    public function __construct(
        EventStoreInterface $eventStore,
        GroupProjectionInterface $groupProjection
    ) {
        $this->eventStore = $eventStore;
        $this->groupProjection = $groupProjection;
    }

    public function get(IdentifiesAggregate $aggregateId)
    {
        $eventStream = $this->eventStore->getAggregateHistoryFor($aggregateId);

        return Group::reconstituteFromEvents($eventStream);
    }

    public function add(RecordsEvents $group)
    {
        $events = $group->getRecordedEvents();
        $this->eventStore->commit($events);
        $this->groupProjection->project($events);

        $group->clearRecordedEvents();
    }

    public function nextIdentity()
    {
        return new GroupId();
    }
}
