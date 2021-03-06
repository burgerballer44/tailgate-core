<?php

namespace Tailgate\Infrastructure\Persistence\Event\Subscriber\Projection;

use Burger\Event\Event;
use Burger\Event\EventPublisherInterface;
use Burger\Event\EventSubscriberInterface;
use Tailgate\Domain\Model\Season\SeasonDomainEvent;
use Tailgate\Infrastructure\Persistence\Projection\SeasonProjectionInterface;

class SeasonProjectorEventSubscriber implements EventSubscriberInterface
{
    private $seasonProjection;

    public function __construct(SeasonProjectionInterface $seasonProjection)
    {
        $this->seasonProjection = $seasonProjection;
    }

    public function handle(Event $event)
    {
        $this->seasonProjection->projectOne($event->getData());
    }

    public function subscribe(EventPublisherInterface $publisher)
    {
        $publisher->on(SeasonDomainEvent::class, [$this, 'handle']);
    }
}
