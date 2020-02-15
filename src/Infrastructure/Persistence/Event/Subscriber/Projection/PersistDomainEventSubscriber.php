<?php

namespace Tailgate\Infrastructure\Persistence\Event\Subscriber\Projection;

use Buttercup\Protects\DomainEvent;
use Burger\Event;
use Burger\EventPublisherInterface;
use Burger\EventSubscriberInterface;
use Tailgate\Domain\Model\Group\GroupDomainEvent;
use Tailgate\Domain\Model\Season\SeasonDomainEvent;
use Tailgate\Domain\Model\Team\TeamDomainEvent;
use Tailgate\Domain\Model\User\UserDomainEvent;
use Tailgate\Infrastructure\Persistence\Event\EventStoreInterface;

class PersistDomainEventSubscriber implements EventSubscriberInterface
{
    private $eventStore;

    public function __construct(EventStoreInterface $eventStore)
    {
        $this->eventStore = $eventStore;
    }

    public function handle(Event $event)
    {
        $this->eventStore->commitOne($event->data);
    }

    public function subscribe(EventPublisherInterface $publisher)
    {
        $publisher->on(DomainEvent::class, [$this, 'handle']);
        $publisher->on(GroupDomainEvent::class, [$this, 'handle']);
        $publisher->on(SeasonDomainEvent::class, [$this, 'handle']);
        $publisher->on(TeamDomainEvent::class, [$this, 'handle']);
        $publisher->on(UserDomainEvent::class, [$this, 'handle']);
    }
}
