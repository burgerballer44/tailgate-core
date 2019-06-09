<?php

namespace Tailgate\Tests\Infrastructure\Persistence\EventStore\PDO;

use Buttercup\Protects\AggregateHistory;
use Buttercup\Protects\DomainEvents;
use PHPUnit\Framework\TestCase;
use Tailgate\Domain\Model\User\UserId;
use Tailgate\Domain\Model\User\UserSignedUp;
use Tailgate\Infrastructure\Persistence\EventStore\PDO\PDOEventStore;

class PDOEventStoreTest extends TestCase
{
    private $pdo;
    private $eventStore;

    public function setUp()
    {
        $connection = getenv('DB_CONNECTION');
        $host = getenv('DB_HOST');
        $port = getenv('DB_PORT');
        $name = getenv('DB_DATABASE');
        $user = getenv('DB_USERNAME');
        $password = getenv('DB_PASSWORD');

        $this->pdo = new \PDO("{$connection}:host={$host};port={$port};dbname={$name};charset=utf8mb4", "{$user}", "{$password}", [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
        ]);

        $this->eventStore = new PDOEventStore($this->pdo);

        $this->pdo->query("
            CREATE TABLE IF NOT EXISTS events (
                aggregate_id VARCHAR(255) NOT NULL,
                type VARCHAR(255) NOT NULL,
                created_at DATETIME NOT NULL,
                data text NOT NULL
            ) ENGINE = INNODB;
        ");
    }

    public function tearDown()
    {
        $this->pdo->query("DROP TABLE IF EXISTS events;");
    }

    public function testItAddsDomainEventsAndCanReturnAggregateHistory()
    {
        $id1 = UserId::fromString('idToCheck1');
        $id2 = UserId::fromString('idToCheck2');
        $id3 = UserId::fromString('idToNotFind');

        $domainEvents = new DomainEvents([
            new UserSignedUp($id1, 'username1', 'password1', 'email1', 'status', 'role'),
            new UserSignedUp($id1, 'username2', 'password2', 'email2', 'status', 'role'),
            new UserSignedUp($id2, 'username3', 'password3', 'email3', 'status', 'role'),
        ]);
        $this->eventStore = new PDOEventStore($this->pdo);

        $history = $this->eventStore->getAggregateHistoryFor($id1);

        $this->assertEmpty(
            $history,
            'failed to return nothing since no events have been added yet'
        );

        $this->eventStore->commit($domainEvents);

        $history1 = $this->eventStore->getAggregateHistoryFor($id1);
        $history2 = $this->eventStore->getAggregateHistoryFor($id2);
        $history3 = $this->eventStore->getAggregateHistoryFor($id3);
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
