<?php

namespace Tailgate\Common\EventPublisher;

interface EventSubscriberInterface
{
    public function isSubscribedTo($aDomainEvent);
    public function handle($aDomainEvent);
}