<?php

namespace Tailgate\Tests\Infrastructure\Persistence\EventStore\InMemory;

use Buttercup\Protects\AggregateHistory;
use Buttercup\Protects\DomainEvents;
use PHPUnit\Framework\TestCase;
use Tailgate\Domain\Model\User\UserId;
use Tailgate\Domain\Model\User\UserRegistered;
use Tailgate\Infrastructure\Persistence\EventStore\InMemory\EventStore;

class InMemoryEventStoreTest extends TestCase
{
    public function testItAddsDomainEventsAndCanReturnAggregateHistory()
    {
        $id1 = UserId::fromString('userId1');
        $id2 = UserId::fromString('userId2');
        $id3 = UserId::fromString('userId3');

        $domainEvents = new DomainEvents([
            new UserRegistered($id1, 'username1', 'password1', 'email1', 'status', 'role', 'randomString'),
            new UserRegistered($id1, 'username2', 'password2', 'email2', 'status', 'role', 'randomString'),
            new UserRegistered($id2, 'username3', 'password3', 'email3', 'status', 'role', 'randomString'),
        ]);
        $eventStore = new EventStore;

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
        $this->assertCount(0, $history3,
            'failed to return nothing since id3 was not added to event store'
        );
    }
}
