<?php

namespace Tailgate\Infrastructure\Persistence\Event;

use Tailgate\Common\Event\Event;
use Tailgate\Common\Event\EventPublisherInterface;
use Tailgate\Common\Event\EventSubscriberInterface;
use Tailgate\Domain\Model\User\UserDomainEvent;
use Tailgate\Domain\Model\User\UserProjectionInterface;

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
