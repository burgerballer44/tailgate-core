<?php

namespace Tailgate\Infrastructure\Persistence\Projection\PDO;

use PDO;
use Tailgate\Domain\Model\User\EmailUpdated;
use Tailgate\Domain\Model\User\PasswordResetTokenApplied;
use Tailgate\Domain\Model\User\PasswordUpdated;
use Tailgate\Domain\Model\User\UserActivated;
use Tailgate\Domain\Model\User\UserDeleted;
use Tailgate\Domain\Model\User\UserRegistered;
use Tailgate\Domain\Model\User\UserUpdated;
use Tailgate\Infrastructure\Persistence\Projection\AbstractProjection;
use Tailgate\Infrastructure\Persistence\Projection\UserProjectionInterface;

class UserProjection extends AbstractProjection implements UserProjectionInterface
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function projectUserRegistered(UserRegistered $event)
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO `user` (user_id, password_hash, email, status, role, created_at) VALUES (:user_id, :password_hash, :email, :status, :role, :created_at)'
        );

        $stmt->execute([
            ':user_id' => $event->getAggregateId(),
            ':password_hash' => $event->getPasswordHash(),
            ':email' => $event->getEmail(),
            ':status' => $event->getStatus(),
            ':role' => $event->getRole(),
            ':created_at' => (new \DateTimeImmutable())->format(self::DATE_FORMAT),
        ]);
    }

    public function projectUserActivated(UserActivated $event)
    {
        $stmt = $this->pdo->prepare(
            'UPDATE `user` SET status = :status WHERE user_id = :user_id'
        );

        $stmt->execute([
            ':user_id' => $event->getAggregateId(),
            ':status' => $event->getStatus(),
        ]);
    }

    public function projectUserDeleted(UserDeleted $event)
    {
        $stmt = $this->pdo->prepare(
            'UPDATE `user` SET status = :status WHERE user_id = :user_id'
        );

        $stmt->execute([
            ':user_id' => $event->getAggregateId(),
            ':status' => $event->getStatus(),
        ]);
    }

    public function projectPasswordUpdated(PasswordUpdated $event)
    {
        $stmt = $this->pdo->prepare(
            'UPDATE `user` SET password_hash = :password_hash WHERE user_id = :user_id'
        );

        $stmt->execute([
            ':user_id' => $event->getAggregateId(),
            ':password_hash' => $event->getPasswordHash(),
        ]);
    }

    public function projectEmailUpdated(EmailUpdated $event)
    {
        $stmt = $this->pdo->prepare(
            'UPDATE `user` SET email = :email WHERE user_id = :user_id'
        );

        $stmt->execute([
            ':user_id' => $event->getAggregateId(),
            ':email' => $event->getEmail(),
        ]);
    }

    public function projectUserUpdated(UserUpdated $event)
    {
        $stmt = $this->pdo->prepare(
            'UPDATE `user` SET email = :email, status = :status, role = :role WHERE user_id = :user_id'
        );

        $stmt->execute([
            ':user_id' => $event->getAggregateId(),
            ':email' => $event->getEmail(),
            ':status' => $event->getStatus(),
            ':role' => $event->getRole(),
        ]);
    }

    public function projectPasswordResetTokenApplied(PasswordResetTokenApplied $event)
    {
        $stmt = $this->pdo->prepare(
            'UPDATE `user` SET password_reset_token = :password_reset_token WHERE user_id = :user_id'
        );

        $stmt->execute([
            ':user_id' => $event->getAggregateId(),
            ':password_reset_token' => $event->getPasswordResetToken(),
        ]);
    }
}
