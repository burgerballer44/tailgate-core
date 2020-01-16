<?php

namespace Tailgate\Domain\Model\User;

use Buttercup\Protects\IdentifiesAggregate;
use Tailgate\Domain\Model\AbstractEntity;
use Tailgate\Domain\Model\ModelException;

class User extends AbstractEntity
{
    const ROLE_USER = 'Normal'; // the average user, normal people who sign up
    const ROLE_ADMIN = 'Admin'; // an important person who can do whatever

    const STATUS_ACTIVE = 'Active'; // can use the app
    const STATUS_PENDING = 'Pending'; // user who signs up but needs to confirm email
    const STATUS_DELETED = 'Deleted'; // user who is deleted

    private $userId;
    private $email;
    private $passwordHash;
    private $status;
    private $role;
    private $passwordResetToken;

    protected function __construct($userId, $email, $passwordHash, $status, $role, $passwordResetToken)
    {
        $this->userId = $userId;
        $this->email = $email;
        $this->passwordHash = $passwordHash;
        $this->status = $status;
        $this->role = $role;
        $this->passwordResetToken = $passwordResetToken;
    }

    // create a user
    public static function create(UserId $userId, $email, $passwordHash)
    {
        $newUser = new User($userId, $email, $passwordHash, User::STATUS_PENDING, User::ROLE_USER, '');

        $newUser->recordThat(
            new UserRegistered($userId, $email, $passwordHash, User::STATUS_PENDING, User::ROLE_USER, '')
        );

        return $newUser;
    }

    // create an empty user
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

    public function getPasswordResetToken()
    {
        return $this->passwordResetToken;
    }

    // set status to active
    public function activate()
    {
        $this->applyAndRecordThat(new UserActivated($this->userId, User::STATUS_ACTIVE));
    }

    // set status to deleted
    public function delete()
    {
        $this->applyAndRecordThat(new UserDeleted($this->userId, User::STATUS_DELETED));
    }

    // updates the password hash
    public function updatePassword($passwordHash)
    {
        $this->applyAndRecordThat(new PasswordUpdated($this->userId, $passwordHash));
    }

    // updates the email
    public function updateEmail($email)
    {
        $this->applyAndRecordThat(new EmailUpdated($this->userId, $email));
    }

    // updates email, status, and role
    public function update($email, $status, $role)
    {
        if (!in_array($role, $this->getValidRoles())) {
            throw new ModelException('Invalid role. Role does not exist.');
        }

        if (!in_array($status, $this->getValidStatuses())) {
            throw new ModelException('Invalid status. Status does not exist.');
        }
        
        $this->applyAndRecordThat(new UserUpdated($this->userId, $email, $status, $role));
    }

    // creates a password reset token
    public function createPasswordResetToken($passwordResetString)
    {
        $token = $this->createTokenFromString($passwordResetString);
        $this->applyAndRecordThat(new PasswordResetTokenCreated($this->userId, $token));
    }

    // create a temporary password reset token
    private function createTokenFromString($passwordResetString)
    {
        return $passwordResetString . '_' . time();
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

    protected function applyPasswordResetTokenCreated(PasswordResetTokenCreated $event)
    {
        $this->passwordResetToken = $event->getPasswordResetToken();
    }

    public static function getValidRoles()
    {
        return [
            self::ROLE_USER,
            self::ROLE_ADMIN,
        ];
    }

    public static function getValidStatuses()
    {
        return [
            self::STATUS_ACTIVE,
            self::STATUS_PENDING,
            self::STATUS_DELETED,
        ];
    }

    // determine if password reset token is valid
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }
        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = 3600; // 1 hour
        return $timestamp + $expire >= time();
    }
}
