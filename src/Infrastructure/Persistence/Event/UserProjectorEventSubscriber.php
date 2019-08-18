<?php

namespace Tailgate\Infrastructure\Persistence\Event;

use Buttercup\Protects\DomainEvent;
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

    public function handle($event)
    {
        $this->userProjection->projectOne($event);
    }

    public function isSubscribedTo($event)
    {
        return $event instanceof UserDomainEvent;
    }
}
