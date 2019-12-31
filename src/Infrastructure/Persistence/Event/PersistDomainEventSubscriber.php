<?php

namespace Tailgate\Infrastructure\Persistence\Event;

use Tailgate\Common\Event\Event;
use Tailgate\Common\Event\EventPublisherInterface;
use Tailgate\Common\Event\EventSubscriberInterface;
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
