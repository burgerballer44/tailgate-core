<?php

namespace Tailgate\Common\EventPublisher;

use Buttercup\Protects\DomainEvent;
use Tailgate\Common\EventPublisher\EventPublisherInterface;

class DomainEventPublisher implements EventPublisherInterface
{
    private $subscribers;
    private static $instance = null;
    private $id = 0;

    public static function instance()
    {
        if (null === static::$instance) {
            static::$instance = new self();
        }

        return static::$instance;
    }

    private function __construct()
    {
        $this->subscribers = [];
    }

    public function __clone()
    {
        throw new \Exception('Clone is not supported');
    }

    public function subscribe($subscriber)
    {
        $id = $this->id;
        $this->subscribers[$id] = $subscriber;
        $this->id++;

        return $id;
    }

    public function unsubscribe($id)
    {
        unset($this->subscribers[$id]);
    }

    public function publish(DomainEvent $event)
    {
        foreach ($this->subscribers as $subscriber) {
            if ($subscriber->isSubscribedTo($event)) {
                $subscriber->handle($event);
            }
        }
    }
}
