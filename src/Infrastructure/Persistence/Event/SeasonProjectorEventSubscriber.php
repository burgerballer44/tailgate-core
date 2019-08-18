<?php

namespace Tailgate\Infrastructure\Persistence\Event;

use Buttercup\Protects\DomainEvent;
use Tailgate\Common\Event\EventSubscriberInterface;
use Tailgate\Domain\Model\Season\SeasonDomainEvent;

class SeasonProjectorEventSubscriber implements EventSubscriberInterface
{
    private $seasonProjection;

    public function __construct(SeasonProjectionInterface $seasonProjection)
    {
        $this->seasonProjection = $seasonProjection;
    }

    public function handle($event)
    {
        $this->seasonProjection->projectOne($event);
    }

    public function isSubscribedTo($event)
    {
        return $event instanceof SeasonDomainEvent;
    }
}
