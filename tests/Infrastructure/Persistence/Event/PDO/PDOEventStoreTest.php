<?php

namespace Tailgate\Tests\Infrastructure\Persistence\Event\PDO;

use Burger\Aggregate\AggregateHistory;
use Burger\Aggregate\DomainEvents;
use Tailgate\Domain\Model\User\UserId;
use Tailgate\Domain\Model\User\UserRegistered;
use Tailgate\Infrastructure\Persistence\Event\PDO\EventStore;
use Tailgate\Test\BaseTestCase;

class PDOEventStoreTest extends BaseTestCase
{
    private $pdoMock;
    private $pdoStatementMock;
    private $eventStore;

    public function setUp(): void
    {
        $this->pdoMock = $this->createMock(\PDO::class);
        $this->pdoStatementMock = $this->createMock(\PDOStatement::class);
        $this->eventStore = new EventStore($this->pdoMock);
    }

    public function testItCanCommitADomainEvent()
    {
        $event = new UserRegistered(UserId::fromString('userId1'), 'email1', 'password1', 'status', 'role', 'randomString');

        // the pdo mock should call prepare and return a pdostatement mock
        $this->pdoMock
            ->expects($this->once())
            ->method('prepare')
            ->with('INSERT INTO event (aggregate_id, type, created_at, data)
            VALUES (:aggregate_id, :type, :created_at, :data)')
            ->willReturn($this->pdoStatementMock);

        // execute method called
        $this->pdoStatementMock
            ->expects($this->once())
            ->method('execute')
             ->with([
                ':aggregate_id' => (string) $event->getAggregateId(),
                ':type' => get_class($event),
                ':created_at' => (new \DateTimeImmutable())->format('Y-m-d H:i:s'),
                ':data' => serialize($event),
            ]);
        ;

        $this->eventStore->commitOne($event);
    }

    public function testItCanCommitManyDomainEvents()
    {
        $domainEvents = new DomainEvents([
            new UserRegistered(UserId::fromString('userId1'), 'email1', 'password1', 'status', 'role', 'randomString'),
            new UserRegistered(UserId::fromString('userId2'), 'email2', 'password2', 'status', 'role', 'randomString'),
            new UserRegistered(UserId::fromString('userId3'), 'email3', 'password3', 'status', 'role', 'randomString'),
        ]);

        // the pdo mock should call prepare and return a pdostatement mock
        $this->pdoMock
            ->expects($this->once())
            ->method('prepare')
            ->with('INSERT INTO event (aggregate_id, type, created_at, data) VALUES (:aggregate_id, :type, :created_at, :data)')
            ->willReturn($this->pdoStatementMock);

        // execute method called three times
        $this->pdoStatementMock
            ->expects($this->exactly(3))
            ->method('execute');

        $this->eventStore->commit($domainEvents);
    }

    public function testItCanGetAnAggregateHistory()
    {
        $id = UserId::fromString('userId');
        $event1 = new UserRegistered($id, 'email1', 'password1', 'status', 'role', 'randomString');
        $event2 = new UserRegistered($id, 'email2', 'password2', 'status', 'role', 'randomString');
        $serializedEvent1 = serialize($event1);
        $serializedEvent2 = serialize($event2);

        $rows = [
            [
                'aggregate_id' => $id,
                'type' => get_class($event1),
                'created_at' => (new \DateTimeImmutable())->format('Y-m-d H:i:s'),
                'data' => $serializedEvent1,
            ], [
                'aggregate_id' => $id,
                'type' => get_class($event2),
                'created_at' => (new \DateTimeImmutable())->format('Y-m-d H:i:s'),
                'data' => $serializedEvent2,
            ],
        ];

        // the pdo mock should call prepare and return a pdostatement mock
        $this->pdoMock
            ->expects($this->once())
            ->method('prepare')
            ->with('SELECT * FROM event WHERE aggregate_id = :aggregate_id')
            ->willReturn($this->pdoStatementMock);

        // execute method called once
        $this->pdoStatementMock
           ->expects($this->once())
           ->method('execute')
           ->with([':aggregate_id' => $id]);

        // fetch method called
        $this->pdoStatementMock
           ->expects($this->exactly(3))
           ->method('fetch')
           ->withConsecutive([], [])
           ->willReturnOnConsecutiveCalls(
               $this->returnValue($rows[0]),
               $this->returnValue($rows[1]),
           );

        $history = $this->eventStore->getAggregateHistoryFor($id);

        $this->assertNotEmpty($history);
        $this->assertTrue(
            $history instanceof AggregateHistory,
            'failed to return an AggregateHistory even though there are two events for id1'
        );
        $this->assertCount(2, $history, 'failed to find both events for id1');
    }
}
