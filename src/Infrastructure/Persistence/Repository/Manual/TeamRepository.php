<?php

namespace Tailgate\Infrastructure\Persistence\Repository\Manual;

use Burger\Aggregate\IdentifiesAggregate;
use Burger\Aggregate\RecordsEvents;
use Tailgate\Infrastructure\Persistence\Event\EventStoreInterface;
use Tailgate\Domain\Model\Team\Team;
use Tailgate\Domain\Model\Team\TeamId;
use Tailgate\Domain\Model\Team\TeamRepositoryInterface;
use Tailgate\Infrastructure\Persistence\Projection\TeamProjectionInterface;

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

        return Team::reconstituteFromEvents($eventStream);
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
