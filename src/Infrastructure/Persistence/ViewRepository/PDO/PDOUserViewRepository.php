<?php

namespace Tailgate\Infrastructure\Persistence\ViewRepository\PDO;

use PDO;
use Tailgate\Domain\Model\User\UserId;
use Tailgate\Domain\Model\User\UserView;
use Tailgate\Domain\Model\User\UserViewRepositoryInterface;

class PDOUserViewRepository implements UserViewRepositoryInterface
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function get(UserId $id)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM user WHERE user_id = :user_id');
        $stmt->execute([':user_id' => (string) $id]);

        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            return new UserView(
                $row['user_id'],
                $row['username'],
                $row['email'],
                $row['status'],
                $row['role']
            );
        }
    }

    public function all()
    {
        $stmt = $this->pdo->prepare('SELECT * FROM user');
        $stmt->execute();

        $users = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $users[] = new UserView(
                $row['user_id'],
                $row['username'],
                $row['email'],
                $row['status'],
                $row['role']
            );
        }

        return $users;
    }

    public function byUsername($username)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM user WHERE username = :username');
        $stmt->execute([':username' => (string) $username]);

        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            return new UserView(
                $row['user_id'],
                $row['username'],
                $row['email'],
                $row['status'],
                $row['role']
            );
        }

        return false;
    }

    public function byEmail($email)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM user WHERE email = :email');
        $stmt->execute([':email' => (string) $email]);

        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            return new UserView(
                $row['user_id'],
                $row['username'],
                $row['email'],
                $row['status'],
                $row['role']
            );
        }

        return false;
    }
}