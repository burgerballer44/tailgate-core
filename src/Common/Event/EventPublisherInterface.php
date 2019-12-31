<?php

namespace Tailgate\Common\Event;

use Tailgate\Common\Event\EventSubscriberInterface;

interface EventPublisherInterface
{
    public static function on($name, $handler, $data, $append);
    public function subscribe(EventSubscriberInterface $subscriber);
    public static function publish($eventName, $data);
}
