<?php

namespace Tailgate\Domain\Model\User;

use Tailgate\Domain\Model\AbstractEntity;
use Buttercup\Protects\IdentifiesAggregate;

class User extends AbstractEntity
{
    const ROLE_USER = 'Normal'; // the average user, normal people who sign up
    const ROLE_ADMIN = 'Admin'; // an important person who can mostly do whatever
    const ROLE_DEVELOPER = 'Developer'; // more than an admin who can do whatever

    const STATUS_ACTIVE = 'Active'; // can use the app
    const STATUS_PENDING = 'Pending'; // user who signs up but needs to confirm email
    const STATUS_INVITED = 'Invited'; // user who was invited by an admin
    const STATUS_DELETED = 'Deleted'; // user who is deleted

    private $userId;
    private $email;
    private $passwordHash;
    private $status;
    private $role;
    private $uniqueKey;

    protected function __construct(
        $userId,
        $email,
        $passwordHash,
        $status,
        $role,
        $uniqueKey
    ) {
        $this->userId = $userId;
        $this->email = $email;
        $this->passwordHash = $passwordHash;
        $this->status = $status;
        $this->role = $role;
        $this->uniqueKey = $uniqueKey;
    }

    public static function create(UserId $userId, $email, $passwordHash, $uniqueKey)
    {
        $newUser = new User(
            $userId,
            $email,
            $passwordHash,
            User::STATUS_PENDING,
            User::ROLE_USER,
            $uniqueKey
        );

        $newUser->recordThat(
            new UserRegistered(
                $userId,
                $email,
                $passwordHash,
                User::STATUS_PENDING,
                User::ROLE_USER,
                $uniqueKey
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

    public function getEmail()
    {
        return $this->email;
    }

    public function getPasswordHash()
    {
        return $this->passwordHash;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function getRole()
    {
        return $this->role;
    }

    public function activate()
    {
        $this->applyAndRecordThat(
            new UserActivated(
                $this->userId,
                User::STATUS_ACTIVE
            )
        );
    }

    public function delete()
    {
        $this->applyAndRecordThat(
            new UserDeleted(
                $this->userId,
                User::STATUS_DELETED
            )
        );
    }

    public function updatePassword($passwordHash)
    {
        $this->applyAndRecordThat(
            new PasswordUpdated(
                $this->userId,
                $passwordHash
            )
        );
    }

    public function updateEmail($email)
    {
        $this->applyAndRecordThat(
            new EmailUpdated(
                $this->userId,
                $email
            )
        );
    }

    public function update($email, $status, $role)
    {
        $this->applyAndRecordThat(
            new UserUpdated(
                $this->userId,
                $email,
                $status,
                $role
            )
        );
    }

    protected function applyUserRegistered(UserRegistered $event)
    {
        $this->email = $event->getEmail();
        $this->passwordHash = $event->getPasswordHash();
        $this->status = $event->getStatus();
        $this->role = $event->getRole();
    }

    protected function applyUserActivated(UserActivated $event)
    {
        $this->status = $event->getStatus();
    }

    protected function applyUserDeleted(UserDeleted $event)
    {
        $this->status = $event->getStatus();
    }

    protected function applyPasswordUpdated(PasswordUpdated $event)
    {
        $this->passwordHash = $event->getPasswordHash();
    }

    protected function applyEmailUpdated(EmailUpdated $event)
    {
        $this->email = $event->getEmail();
    }

    protected function applyUserUpdated(UserUpdated $event)
    {
        $this->email = $event->getEmail();
        $this->status = $event->getStatus();
        $this->role = $event->getRole();
    }
}
