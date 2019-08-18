<?php

namespace Tailgate\Common\Event;

use Buttercup\Protects\DomainEvent;
use Tailgate\Common\Event\EventStoreInterface;
use Tailgate\Common\Event\EventSubscriberInterface;

class PersistDomainEventSubscriber implements EventSubscriberInterface
{
    private $eventStore;

    public function __construct(EventStoreInterface $eventStore)
    {
        $this->eventStore = $eventStore;
    }

    public function handle($event)
    {
        $this->eventStore->commitOne($event);
    }

    public function isSubscribedTo($event)
    {
        return $event instanceof DomainEvent;
    }
}
