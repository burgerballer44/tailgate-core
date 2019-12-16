<?php

namespace Tailgate\Infrastructure\Persistence\Repository\Publisher;

use Buttercup\Protects\IdentifiesAggregate;
use Buttercup\Protects\RecordsEvents;
use Tailgate\Infrastructure\Persistence\Event\EventStoreInterface;
use Tailgate\Domain\Model\User\User;
use Tailgate\Domain\Model\User\UserId;
use Tailgate\Domain\Model\User\UserRepositoryInterface;
use Tailgate\Common\Event\EventPublisherInterface;

class UserRepository implements UserRepositoryInterface
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

    public function get(IdentifiesAggregate $aggregateId)
    {
        $eventStream = $this->eventStore->getAggregateHistoryFor($aggregateId);

        return User::reconstituteFrom($eventStream);
    }

    public function add(RecordsEvents $user)
    {
        $events = $user->getRecordedEvents();

        foreach ($events as $event) {
            $this->domainEventPublisher->publish($event);
        }

        $user->clearRecordedEvents();
    }

    public function nextIdentity()
    {
        return new UserId();
    }
}
