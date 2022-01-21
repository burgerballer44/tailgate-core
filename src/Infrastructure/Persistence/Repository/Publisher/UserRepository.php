<?php

namespace Tailgate\Infrastructure\Persistence\Repository\Publisher;

use Burger\Aggregate\IdentifiesAggregate;
use Burger\Aggregate\IsEventSourced;
use Burger\Aggregate\RecordsEvents;
use Burger\Event\EventPublisherInterface;
use Tailgate\Domain\Model\User\User;
use Tailgate\Domain\Model\User\UserDomainEvent;
use Tailgate\Domain\Model\User\UserId;
use Tailgate\Domain\Model\User\UserRepositoryInterface;
use Tailgate\Infrastructure\Persistence\Event\EventStoreInterface;

class UserRepository implements UserRepositoryInterface
{
    private $eventStore;
    private $eventPublisher;

    public function __construct(
        EventStoreInterface $eventStore,
        EventPublisherInterface $eventPublisher
    ) {
        $this->eventStore = $eventStore;
        $this->eventPublisher = $eventPublisher;
    }

    public function get(IdentifiesAggregate $aggregateId) : IsEventSourced
    {
        $eventStream = $this->eventStore->getAggregateHistoryFor($aggregateId);

        return User::reconstituteFromEvents($eventStream);
    }

    public function add(RecordsEvents $user)
    {
        $events = $user->getRecordedEvents();

        foreach ($events as $event) {
            $this->eventPublisher->publish(UserDomainEvent::class, $event);
        }

        $user->clearRecordedEvents();
    }

    public function nextIdentity()
    {
        return new UserId();
    }
}
