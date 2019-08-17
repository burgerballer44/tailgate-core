<?php

namespace Ddd\Domain;

use Buttercup\Protects\DomainEvent;
use PHPUnit\Framework\TestCase;
use Tailgate\Common\EventPublisher\DomainEventPublisher;
use Tailgate\Common\EventPublisher\EventSubscriberInterface;

class DomainEventPublisherTest extends TestCase
{
    private $publisher;

    public function setUp()
    {
        $this->publisher = DomainEventPublisher::instance();
    }

    public function testCallingByInstanceMethodReturnsSameObject()
    {
        $anotherPublisher = DomainEventPublisher::instance();
        $this->assertSame($this->publisher, $anotherPublisher);
    }

    public function testItShouldNotifySubscriber()
    {   
        $subscriber = $this->getSpySubscriber('test-event');
        $domainEvent = $this->getFakeDomainEvent('test-event');

        $this->publisher->subscribe($subscriber);
        $this->publisher->publish($domainEvent);

        $this->assertTrue($subscriber->isHandled);
        $this->assertEquals($domainEvent, $subscriber->domainEvent);
    }

    public function testNotSubscribedSubscribersShouldNotBeNotified()
    {   
        $subscriber = $this->getSpySubscriber('test-event');
        $domainEvent = $this->getFakeDomainEvent('different-test-event');

        $this->publisher->subscribe($subscriber);
        $this->publisher->publish($domainEvent);

        $this->assertFalse($subscriber->isHandled);
        $this->assertNull($subscriber->domainEvent);
    }

    public function testItShouldUnsubscribeSubscriber()
    {   
        $subscriber = $this->getSpySubscriber('test-event');
        $domainEvent = $this->getFakeDomainEvent('test-event');

        $subscriberId = $this->publisher->subscribe($subscriber);
        $this->publisher->unsubscribe($subscriberId);
        $this->publisher->publish($domainEvent);

        $this->assertFalse($subscriber->isHandled);
        $this->assertNull($subscriber->domainEvent);
    }

    private function getSpySubscriber($eventName)
    {
        return new class($eventName) implements EventSubscriberInterface {
            public $domainEvent;
            public $isHandled = false;
            private $eventName;

            public function __construct($eventName)
            {
                $this->eventName = $eventName;
            }

            public function isSubscribedTo($aDomainEvent)
            {
                return $this->eventName === $aDomainEvent->name;
            }

            public function handle($aDomainEvent)
            {
                $this->domainEvent = $aDomainEvent;
                $this->isHandled = true;
            }
        };
    }

    private function getFakeDomainEvent($eventName)
    {
        return new class($eventName) implements DomainEvent {
            public $name;

            public function __construct($name)
            {
                $this->name = $name;
            }

            public function getAggregateId() {}
        };
    }

}