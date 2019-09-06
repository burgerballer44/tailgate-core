<?php

namespace Tailgate\Infrastructure\Persistence\Event\PDO;

use Buttercup\Protects\AggregateHistory;
use Buttercup\Protects\DomainEvent;
use Buttercup\Protects\DomainEvents;
use Buttercup\Protects\IdentifiesAggregate;
use Tailgate\Common\Event\EventStoreInterface;
use PDO;

class EventStore implements EventStoreInterface
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function commitOne(DomainEvent $event)
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO event (aggregate_id, type, created_at, data)
            VALUES (:aggregate_id, :type, :created_at, :data)'
        );

        $stmt->execute([
            ':aggregate_id' => (string) $event->getAggregateId(),
            ':type' => get_class($event),
            ':created_at' => (new \DateTimeImmutable())->format('Y-m-d H:i:s'),
            ':data' => serialize($event)
        ]);
    }

    public function commit(DomainEvents $events)
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO event (aggregate_id, type, created_at, data) VALUES (:aggregate_id, :type, :created_at, :data)'
        );

        foreach ($events as $event) {
            $stmt->execute([
                ':aggregate_id' => (string) $event->getAggregateId(),
                ':type' => get_class($event),
                ':created_at' => (new \DateTimeImmutable())->format('Y-m-d H:i:s'),
                ':data' => serialize($event)
            ]);
        }
    }

    public function getAggregateHistoryFor(IdentifiesAggregate $id)
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM event WHERE aggregate_id = :aggregate_id'
        );
        $stmt->execute([':aggregate_id' => (string) $id]);

        $events = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $events[] = unserialize($row['data']);
        }

        return new AggregateHistory($id, $events);
    }
}
