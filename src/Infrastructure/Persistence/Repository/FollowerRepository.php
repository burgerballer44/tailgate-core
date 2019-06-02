<?php

namespace Tailgate\Infrastructure\Persistence\Repository;

use Buttercup\Protects\IdentifiesAggregate;
use Buttercup\Protects\RecordsEvents;
use Tailgate\Common\EventStore\EventStoreInterface;
use Tailgate\Domain\Model\Follower\Follower;
use Tailgate\Domain\Model\Follower\FollowerId;
use Tailgate\Domain\Model\Follower\FollowerRepositoryInterface;
use Tailgate\Domain\Model\Follower\FollowerProjectionInterface;

class FollowerRepository implements FollowerRepositoryInterface
{
    private $eventStore;
    private $followerProjection;

    public function __construct(
        EventStoreInterface $eventStore,
        FollowerProjectionInterface $followerProjection
    ) {
        $this->eventStore = $eventStore;
        $this->followerProjection = $followerProjection;
    }

    public function get(IdentifiesAggregate $aggregateId)
    {
        $eventStream = $this->eventStore->getAggregateHistoryFor($aggregateId);

        return Follower::reconstituteFrom($eventStream);
    }

    public function add(RecordsEvents $follower)
    {
        $events = $follower->getRecordedEvents();
        $this->eventStore->commit($events);
        $this->followerProjection->project($events);

        $follower->clearRecordedEvents();
    }

    public function nextIdentity()
    {
        return new FollowerId();
    }
}