<?php

namespace Tailgate\Infrastructure\Persistence\Projection\PDO;

use PDO;
use Tailgate\Domain\Model\Team\TeamAdded;
use Tailgate\Domain\Model\Team\TeamFollowed;
use Tailgate\Domain\Model\Team\TeamProjectionInterface;
use Tailgate\Infrastructure\Persistence\Projection\AbstractProjection;

class PDOTeamProjection extends AbstractProjection implements TeamProjectionInterface
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function projectTeamAdded(TeamAdded $event)
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO team (user_id, username, password_hash, email, status, role, created_at)
            VALUES (:user_id, :username, :password_hash, :email, :status, :role, :created_at)'
        );

        $stmt->execute([
            ':user_id' => $event->getAggregateId(),
            ':username' => $event->getUsername(),
            ':password_hash' => $event->getPasswordHash(),
            ':email' => $event->getEmail(),
            ':status' => $event->getStatus(),
            ':role' => $event->getRole(),
            ':created_at' => (new \DateTimeImmutable())->format('Y-m-d H:i:s')
        ]);
    }

    public function projectTeamFollowed(TeamFollowed $event)
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO user (user_id, username, password_hash, email, status, role, created_at)
            VALUES (:user_id, :username, :password_hash, :email, :status, :role, :created_at)'
        );

        $stmt->execute([
            ':user_id' => $event->getAggregateId(),
            ':username' => $event->getUsername(),
            ':password_hash' => $event->getPasswordHash(),
            ':email' => $event->getEmail(),
            ':status' => $event->getStatus(),
            ':role' => $event->getRole(),
            ':created_at' => (new \DateTimeImmutable())->format('Y-m-d H:i:s')
        ]);
    }
}