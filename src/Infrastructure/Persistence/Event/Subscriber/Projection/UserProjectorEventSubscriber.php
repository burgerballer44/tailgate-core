<?php

namespace Tailgate\Infrastructure\Persistence\Event\Subscriber\Projection;

use Burger\Event\Event;
use Burger\Event\EventPublisherInterface;
use Burger\Event\EventSubscriberInterface;
use Tailgate\Domain\Model\User\UserDomainEvent;
use Tailgate\Infrastructure\Persistence\Projection\UserProjectionInterface;

class UserProjectorEventSubscriber implements EventSubscriberInterface
{
    private $userProjection;

    public function __construct(UserProjectionInterface $userProjection)
    {
        $this->userProjection = $userProjection;
    }

    public function handle(Event $event)
    {
        $this->userProjection->projectOne($event->getData());
    }

    public function subscribe(EventPublisherInterface $publisher)
    {
        $publisher->on(UserDomainEvent::class, [$this, 'handle']);
    }
}
