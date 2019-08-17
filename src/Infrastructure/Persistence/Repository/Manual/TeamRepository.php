<?php

namespace Tailgate\Infrastructure\Persistence\Repository;

use Buttercup\Protects\IdentifiesAggregate;
use Buttercup\Protects\RecordsEvents;
use Tailgate\Common\EventStore\EventStoreInterface;
use Tailgate\Domain\Model\Team\Team;
use Tailgate\Domain\Model\Team\TeamId;
use Tailgate\Domain\Model\Team\TeamRepositoryInterface;
use Tailgate\Domain\Model\Team\TeamProjectionInterface;

class TeamRepository implements TeamRepositoryInterface
{
    private $eventStore;
    private $teamProjection;

    public function __construct(
        EventStoreInterface $eventStore,
        TeamProjectionInterface $teamProjection
    ) {
        $this->eventStore = $eventStore;
        $this->teamProjection = $teamProjection;
    }

    public function get(IdentifiesAggregate $aggregateId)
    {
        $eventStream = $this->eventStore->getAggregateHistoryFor($aggregateId);

        return Team::reconstituteFrom($eventStream);
    }

    public function add(RecordsEvents $team)
    {
        $events = $team->getRecordedEvents();
        $this->eventStore->commit($events);
        $this->teamProjection->project($events);

        $team->clearRecordedEvents();
    }

    public function nextIdentity()
    {
        return new TeamId();
    }
}
