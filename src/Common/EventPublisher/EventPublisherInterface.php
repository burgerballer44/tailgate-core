<?php

namespace Tailgate\Common\EventPublisher;

use Buttercup\Protects\DomainEvent;

interface EventPublisherInterface
{
    public function subscribe($subscriber);
    public function unsubscribe($id);
    public function publish(DomainEvent $event);
}