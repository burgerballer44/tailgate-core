<?php

namespace Tailgate\Infrastructure\Persistence\Repository;

use Buttercup\Protects\IdentifiesAggregate;
use Buttercup\Protects\RecordsEvents;
use Tailgate\Common\EventStore\EventStoreInterface;
use Tailgate\Domain\Model\Season\Season;
use Tailgate\Domain\Model\Season\SeasonId;
use Tailgate\Domain\Model\Season\SeasonRepositoryInterface;
use Tailgate\Domain\Model\Season\SeasonProjectionInterface;

class SeasonRepository implements SeasonRepositoryInterface
{
    private $eventStore;
    private $seasonProjection;

    public function __construct(
        EventStoreInterface $eventStore,
        SeasonProjectionInterface $seasonProjection
    ) {
        $this->eventStore = $eventStore;
        $this->seasonProjection = $seasonProjection;
    }

    public function get(IdentifiesAggregate $aggregateId)
    {
        $eventStream = $this->eventStore->getAggregateHistoryFor($aggregateId);

        return Season::reconstituteFrom($eventStream);
    }

    public function add(RecordsEvents $season)
    {
        $events = $season->getRecordedEvents();
        $this->eventStore->commit($events);
        $this->seasonProjection->project($events);

        $season->clearRecordedEvents();
    }

    public function nextIdentity()
    {
        return new SeasonId();
    }
}