<?php

namespace Tailgate\Infrastructure\Persistence\Event\Subscriber\Projection;

use Burger\Event;
use Burger\EventPublisherInterface;
use Burger\EventSubscriberInterface;
use Tailgate\Domain\Model\Team\TeamDomainEvent;
use Tailgate\Infrastructure\Persistence\Projection\TeamProjectionInterface;

class TeamProjectorEventSubscriber implements EventSubscriberInterface
{
    private $teamProjection;

    public function __construct(TeamProjectionInterface $teamProjection)
    {
        $this->teamProjection = $teamProjection;
    }

    public function handle(Event $event)
    {
        $this->teamProjection->projectOne($event->data);
    }

    public function subscribe(EventPublisherInterface $publisher)
    {
        $publisher->on(TeamDomainEvent::class, [$this, 'handle']);
    }
}
