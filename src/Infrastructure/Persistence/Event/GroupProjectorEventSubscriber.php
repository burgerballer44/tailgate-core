<?php

namespace Tailgate\Infrastructure\Persistence\Event;

use Tailgate\Common\Event\Event;
use Tailgate\Common\Event\EventPublisherInterface;
use Tailgate\Common\Event\EventSubscriberInterface;
use Tailgate\Domain\Model\Group\GroupDomainEvent;
use Tailgate\Domain\Model\Group\GroupProjectionInterface;

class GroupProjectorEventSubscriber implements EventSubscriberInterface
{
    private $groupProjection;

    public function __construct(GroupProjectionInterface $groupProjection)
    {
        $this->groupProjection = $groupProjection;
    }

    public function handle(Event $event)
    {
        $this->groupProjection->projectOne($event->data);
    }

    public function subscribe(EventPublisherInterface $publisher)
    {
        $publisher->on(GroupDomainEvent::class, [$this, 'handle']);
    }
}
