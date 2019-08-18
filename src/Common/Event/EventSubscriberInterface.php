<?php

namespace Tailgate\Common\Event;

interface EventSubscriberInterface
{
    public function isSubscribedTo($aDomainEvent);
    public function handle($aDomainEvent);
}
