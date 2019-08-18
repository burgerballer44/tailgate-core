<?php

namespace Tailgate\Common\Event;

use Buttercup\Protects\DomainEvent;

interface EventPublisherInterface
{
    public function subscribe($subscriber);
    public function unsubscribe($id);
    public function publish(DomainEvent $event);
}