<?php

namespace Tailgate\Infrastructure\Persistence\Event;

use Tailgate\Common\Event\Event;
use Tailgate\Common\Event\EventPublisherInterface;
use Tailgate\Common\Event\EventSubscriberInterface;
use Tailgate\Domain\Model\Team\TeamDomainEvent;
use Tailgate\Domain\Model\Team\TeamProjectionInterface;

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
