<?php

namespace Tailgate\Infrastructure\Persistence\Event;

use Burger\Event;
use Burger\EventPublisherInterface;
use Burger\EventSubscriberInterface;
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
        $this->userProjection->projectOne($event->data);
    }

    public function subscribe(EventPublisherInterface $publisher)
    {
        $publisher->on(UserDomainEvent::class, [$this, 'handle']);
    }
}
