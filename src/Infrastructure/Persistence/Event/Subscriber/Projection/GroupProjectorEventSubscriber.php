<?php

namespace Tailgate\Infrastructure\Persistence\Event\Subscriber\Projection;

use Burger\Event\Event;
use Burger\Event\EventPublisherInterface;
use Burger\Event\EventSubscriberInterface;
use Tailgate\Domain\Model\Group\GroupDomainEvent;
use Tailgate\Infrastructure\Persistence\Projection\GroupProjectionInterface;

class GroupProjectorEventSubscriber implements EventSubscriberInterface
{
    private $groupProjection;

    public function __construct(GroupProjectionInterface $groupProjection)
    {
        $this->groupProjection = $groupProjection;
    }

    public function handle(Event $event)
    {
        $this->groupProjection->projectOne($event->getData());
    }

    public function subscribe(EventPublisherInterface $publisher)
    {
        $publisher->on(GroupDomainEvent::class, [$this, 'handle']);
    }
}
