<?php

namespace Tailgate\Infrastructure\Persistence\Event;

use Tailgate\Common\Event\Event;
use Tailgate\Common\Event\EventPublisherInterface;
use Tailgate\Common\Event\EventSubscriberInterface;
use Tailgate\Domain\Model\Season\SeasonDomainEvent;
use Tailgate\Domain\Model\Season\SeasonProjectionInterface;

class SeasonProjectorEventSubscriber implements EventSubscriberInterface
{
    private $seasonProjection;

    public function __construct(SeasonProjectionInterface $seasonProjection)
    {
        $this->seasonProjection = $seasonProjection;
    }

    public function handle(Event $event)
    {
        $this->seasonProjection->projectOne($event->data);
    }

    public function subscribe(EventPublisherInterface $publisher)
    {
        $publisher->on(SeasonDomainEvent::class, [$this, 'handle']);
    }
}
