<?php

namespace Tailgate\Common\Event;

use Tailgate\Common\Event\EventPublisherInterface;

interface EventSubscriberInterface
{
    public function subscribe(EventPublisherInterface $publisher);
}
