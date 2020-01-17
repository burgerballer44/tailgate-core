<?php

namespace Tailgate\Infrastructure\Persistence\Repository\Publisher;

use Buttercup\Protects\IdentifiesAggregate;
use Buttercup\Protects\RecordsEvents;
use Burger\EventPublisherInterface;
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

    public function get(IdentifiesAggregate $aggregateId)
    {
        $eventStream = $this->eventStore->getAggregateHistoryFor($aggregateId);

        return User::reconstituteFrom($eventStream);
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
