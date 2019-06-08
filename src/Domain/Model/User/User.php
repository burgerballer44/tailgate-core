<?php

namespace Tailgate\Domain\Model\User;

use Tailgate\Domain\Model\AbstractEntity;
use Buttercup\Protects\IdentifiesAggregate;

class User extends AbstractEntity
{
    const ROLE_USER = 10; // the average user, normal people who sign up
    const ROLE_ADMIN = 20; // an important person who can mostly do whatever
    const ROLE_DEVELOPER = 30; // more than an admin who can do whatever

    const STATUS_ACTIVE = 10; // can use the app
    const STATUS_PENDING = 20; // user who signs up but needs to confirm email
    const STATUS_INVITED = 30; // user who was invited by an admin
    const STATUS_DELETED = 99; // user who is deleted

    private $userId;
    private $username;
    private $passwordHash;
    private $email;
    private $status;
    private $role;
    private $recordedEvents = [];

    protected function __construct(
        $userId,
        $username,
        $passwordHash,
        $email,
        $status,
        $role
    ) {
        $this->userId = $userId;
        $this->username = $username;
        $this->passwordHash = $passwordHash;
        $this->email = $email;
        $this->status = $status;
        $this->role = $role;
    }

    public static function create(UserId $userId, $username, $passwordHash, $email)
    {
        $newUser = new User(
            $userId,
            $username,
            $passwordHash,
            $email,
            User::STATUS_PENDING,
            User::ROLE_USER
        );

        $newUser->recordThat(
            new UserSignedUp(
                $userId,
                $username,
                $passwordHash,
                $email,
                User::STATUS_PENDING,
                User::ROLE_USER
            )
        );

        return $newUser;
    }

    protected static function createEmptyEntity(IdentifiesAggregate $userId)
    {
        return new User($userId, '', '', '', '', '');
    }

    public function getId()
    {
        return (string) $this->userId;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getPasswordHash()
    {
        return $this->passwordHash;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function getRole()
    {
        return $this->role;
    }

    protected function applyUserSignedUp(UserSignedUp $event)
    {
        $this->username = $event->getUsername();
        $this->passwordHash = $event->getPasswordHash();
        $this->email = $event->getEmail();
        $this->status = $event->getStatus();
        $this->role = $event->getRole();
    }
}
