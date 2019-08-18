<?php

namespace Tailgate\Infrastructure\Persistence\Event;

use Buttercup\Protects\DomainEvent;
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

    public function handle($event)
    {
        $this->groupProjection->projectOne($event);
    }

    public function isSubscribedTo($event)
    {
        return $event instanceof GroupDomainEvent;
    }
}
