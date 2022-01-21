<?php

namespace Tailgate\Infrastructure\Persistence\Repository\Manual;

use Burger\Aggregate\IdentifiesAggregate;
use Burger\Aggregate\IsEventSourced;
use Burger\Aggregate\RecordsEvents;
use Tailgate\Domain\Model\User\User;
use Tailgate\Domain\Model\User\UserId;
use Tailgate\Domain\Model\User\UserRepositoryInterface;
use Tailgate\Infrastructure\Persistence\Event\EventStoreInterface;
use Tailgate\Infrastructure\Persistence\Projection\UserProjectionInterface;

class UserRepository implements UserRepositoryInterface
{
    private $eventStore;
    private $userProjection;

    public function __construct(
        EventStoreInterface $eventStore,
        UserProjectionInterface $userProjection
    ) {
        $this->eventStore = $eventStore;
        $this->userProjection = $userProjection;
    }

    public function get(IdentifiesAggregate $aggregateId) : IsEventSourced
    {
        $eventStream = $this->eventStore->getAggregateHistoryFor($aggregateId);

        return User::reconstituteFromEvents($eventStream);
    }

    public function add(RecordsEvents $user)
    {
        $events = $user->getRecordedEvents();
        $this->eventStore->commit($events);
        $this->userProjection->project($events);

        $user->clearRecordedEvents();
    }

    public function nextIdentity()
    {
        return new UserId();
    }
}
