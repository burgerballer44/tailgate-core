<?php

namespace Tailgate\Infrastructure\Persistence\Event;

use Buttercup\Protects\DomainEvent;
use Tailgate\Common\Event\EventSubscriberInterface;
use Tailgate\Domain\Model\Team\TeamDomainEvent;

class TeamProjectorEventSubscriber implements EventSubscriberInterface
{
    private $teamProjection;

    public function __construct(TeamProjectionInterface $teamProjection)
    {
        $this->teamProjection = $teamProjection;
    }

    public function handle($event)
    {
        $this->teamProjection->projectOne($event);
    }

    public function isSubscribedTo($event)
    {
        return $event instanceof TeamDomainEvent;
    }
}
