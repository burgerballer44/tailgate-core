<?php

namespace Tailgate\Infrastructure\Persistence\Projection\PDO;

use PDO;
use Tailgate\Domain\Model\User\UserProjectionInterface;
use Tailgate\Domain\Model\User\UserSignedUp;
use Tailgate\Domain\Model\User\UserActivated;
use Tailgate\Domain\Model\User\PasswordUpdated;
use Tailgate\Domain\Model\User\EmailUpdated;
use Tailgate\Infrastructure\Persistence\Projection\AbstractProjection;

class UserProjection extends AbstractProjection implements UserProjectionInterface
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function projectUserSignedUp(UserSignedUp $event)
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO `user` (user_id, username, password_hash, email, status, role, key, created_at)
            VALUES (:user_id, :username, :password_hash, :email, :status, :role, :key, :created_at)'
        );

        $stmt->execute([
            ':user_id' => $event->getAggregateId(),
            ':username' => $event->getUsername(),
            ':password_hash' => $event->getPasswordHash(),
            ':email' => $event->getEmail(),
            ':status' => $event->getStatus(),
            ':role' => $event->getRole(),
            ':key' => $event->getKey(),
            ':created_at' => (new \DateTimeImmutable())->format('Y-m-d H:i:s')
        ]);
    }

    public function projectUserActivated(UserActivated $event)
    {
        $stmt = $this->pdo->prepare(
            'UPDATE `user`
            SET status = :status
            WHERE user_id = :user_id'
        );

        $stmt->execute([
            ':user_id' => $event->getAggregateId(),
            ':status' => $event->getStatus(),
        ]);
    }

    public function projectPasswordUpdated(PasswordUpdated $event)
    {
        $stmt = $this->pdo->prepare(
            'UPDATE `user`
            SET password_hash = :password_hash
            WHERE user_id = :user_id'
        );

        $stmt->execute([
            ':user_id' => $event->getAggregateId(),
            ':password_hash' => $event->getPasswordHash(),
        ]);
    }

    public function projectEmailUpdated(EmailUpdated $event)
    {
        $stmt = $this->pdo->prepare(
            'UPDATE `user`
            SET email = :email
            WHERE user_id = :user_id'
        );

        $stmt->execute([
            ':user_id' => $event->getAggregateId(),
            ':email' => $event->getEmail(),
        ]);
    }
}