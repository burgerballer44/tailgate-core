<?php

namespace Tailgate\Common\EventPublisher;

class PersistDomainEventSubscriber implements EventSubscriberInterface
{
    /**
     * @var EventStore
     */
    private $eventStore;

    public function __construct($anEventStore)
    {
        $this->eventStore = $anEventStore;
    }

    public function handle($aDomainEvent)
    {
        $this->eventStore->append($aDomainEvent);
    }

    public function isSubscribedTo($aDomainEvent)
    {
        return $aDomainEvent instanceof DomainEvent;
    }
}
