<?php

namespace Tailgate\Infrastructure\Persistence\ViewRepository\PDO;

use PDO;
use Tailgate\Domain\Model\User\UserId;
use Tailgate\Domain\Model\User\UserView;
use Tailgate\Domain\Model\User\UserViewRepositoryInterface;
use Tailgate\Infrastructure\Persistence\ViewRepository\RepositoryException;

class UserViewRepository implements UserViewRepositoryInterface
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function get(UserId $id)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM `user` WHERE user_id = :user_id LIMIT 1');
        $stmt->execute([':user_id' => (string) $id]);

        if (!$row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            throw new RepositoryException("User not found.");
        }

        return new UserView(
            $row['user_id'],
            $row['email'],
            $row['status'],
            $row['role']
        );
    }

    public function all()
    {
        $stmt = $this->pdo->prepare('SELECT * FROM `user`');
        $stmt->execute();

        $users = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $users[] = new UserView(
                $row['user_id'],
                $row['email'],
                $row['status'],
                $row['role']
            );
        }

        return $users;
    }

    public function byEmail($email)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM `user` WHERE email = :email LIMIT 1');
        $stmt->execute([':email' => (string) $email]);

        if (!$row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            throw new RepositoryException("User not found by email.");
        }

        return new UserView(
            $row['user_id'],
            $row['email'],
            $row['status'],
            $row['role']
        );
    }

    public function byPasswordResetToken($passwordResetToken)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM `user` WHERE password_reset_token = :password_reset_token LIMIT 1');
        $stmt->execute([':password_reset_token' => (string) $passwordResetToken]);

        if (!$row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            throw new RepositoryException("User not found by reset token.");
        }

        return new UserView(
            $row['user_id'],
            $row['email'],
            $row['status'],
            $row['role']
        );
    }
}
