<?php

namespace Tailgate\Tests\Infrastructure\Persistence\Event\InMemory;

use Burger\Aggregate\AggregateHistory;
use Burger\Aggregate\DomainEvents;
use Tailgate\Domain\Model\Common\Date;
use Tailgate\Domain\Model\Common\Email;
use Tailgate\Domain\Model\User\UserId;
use Tailgate\Domain\Model\User\UserRegistered;
use Tailgate\Domain\Model\User\UserRole;
use Tailgate\Domain\Model\User\UserStatus;
use Tailgate\Infrastructure\Persistence\Event\InMemory\EventStore;
use Tailgate\Test\BaseTestCase;

class InMemoryEventStoreTest extends BaseTestCase
{
    public function testItCanCommitOneDomainEvent()
    {
        $id = UserId::fromString('userId1');
        $event = new UserRegistered($id, Email::fromString('email1@email.com'), 'password1', UserStatus::fromString('Active'), UserRole::fromString('Admin'), Date::fromDateTimeImmutable($this->getFakeTime()->currentTime()));
        $eventStore = new EventStore();

        $history = $eventStore->getAggregateHistoryFor($id);
        $this->assertEmpty(
            $history,
            'failed to return nothing since no events have been added yet'
        );

        $eventStore->commitOne($event);

        $history = $eventStore->getAggregateHistoryFor($id);
        $this->assertTrue(
            $history instanceof AggregateHistory,
            'failed to return an AggregateHistory even though there are two events for id1'
        );
        $this->assertCount(1, $history, 'failed to find the event for id');
    }

    public function testItCommitManyDomainEventsAndCanReturnAggregateHistory()
    {
        $id1 = UserId::fromString('userId1');
        $id2 = UserId::fromString('userId2');
        $id3 = UserId::fromString('userId3');

        $domainEvents = new DomainEvents([
            new UserRegistered($id1, Email::fromString('email1@email.com'), 'password1', UserStatus::fromString('Active'), UserRole::fromString('Admin'), Date::fromDateTimeImmutable($this->getFakeTime()->currentTime())),
            new UserRegistered($id1, Email::fromString('email2@email.com'), 'password2', UserStatus::fromString('Active'), UserRole::fromString('Admin'), Date::fromDateTimeImmutable($this->getFakeTime()->currentTime())),
            new UserRegistered($id2, Email::fromString('email3@email.com'), 'password3', UserStatus::fromString('Active'), UserRole::fromString('Admin'), Date::fromDateTimeImmutable($this->getFakeTime()->currentTime())),
        ]);
        $eventStore = new EventStore();

        $history = $eventStore->getAggregateHistoryFor($id1);
        $this->assertEmpty(
            $history,
            'failed to return nothing since no events have been added yet'
        );

        $eventStore->commit($domainEvents);

        $history1 = $eventStore->getAggregateHistoryFor($id1);
        $history2 = $eventStore->getAggregateHistoryFor($id2);
        $history3 = $eventStore->getAggregateHistoryFor($id3);
        $this->assertTrue(
            $history1 instanceof AggregateHistory,
            'failed to return an AggregateHistory even though there are two events for id1'
        );
        $this->assertCount(2, $history1, 'failed to find both events for id1');
        $this->assertTrue(
            $history2 instanceof AggregateHistory,
            'failed to return an AggregateHistory for id2'
        );
        $this->assertCount(
            0,
            $history3,
            'failed to return nothing since id3 was not added to event store'
        );
    }
}
