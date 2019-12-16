<?php

namespace Tailgate\Infrastructure\Persistence\Repository\Manual;

use Buttercup\Protects\IdentifiesAggregate;
use Buttercup\Protects\RecordsEvents;
use Tailgate\Infrastructure\Persistence\Event\EventStoreInterface;
use Tailgate\Domain\Model\Group\Group;
use Tailgate\Domain\Model\Group\GroupId;
use Tailgate\Domain\Model\Group\GroupRepositoryInterface;
use Tailgate\Domain\Model\Group\GroupProjectionInterface;

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

        return Group::reconstituteFrom($eventStream);
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
