<?php

namespace Tailgate\Domain\Model\User;

use Burger\Aggregate\IdentifiesAggregate;
use Tailgate\Domain\Model\AbstractEventBasedEntity;
use Tailgate\Domain\Model\Common\Date;
use Tailgate\Domain\Model\Common\Email;

class User extends AbstractEventBasedEntity
{
    private $userId;
    private $email;
    private $passwordHash;
    private $status;
    private $role;
    private $passwordResetToken;

    public function getUserId()
    {
        return $this->userId;
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

    // create an empty user
    protected static function createEmptyEntity(IdentifiesAggregate $userId)
    {
        return new static();
    }

    // register a user to use application
    public static function register(UserId $userId, Email $email, $passwordHash, Date $dateOccurred)
    {
        $user = new static();

        $user->applyAndRecordThat(
            new UserRegistered($userId, $email, $passwordHash, UserStatus::getPending(), UserRole::getStandard(), $dateOccurred)
        );

        return $user;
    }

    // set user status to active
    public function activate(Date $dateOccurred)
    {
        $this->applyAndRecordThat(new UserActivated($this->userId, UserStatus::getActive(), $dateOccurred));
    }

    // update the password hash
    public function updatePassword($passwordHash, Date $dateOccurred)
    {
        $this->applyAndRecordThat(new PasswordUpdated($this->userId, $passwordHash, $dateOccurred));
    }

    // update the user email address
    public function updateEmail(Email $email, Date $dateOccurred)
    {
        $this->applyAndRecordThat(new EmailUpdated($this->userId, $email, $dateOccurred));
    }

    // update user email, status, and role
    public function update(Email $email, UserStatus $status, UserRole $role, Date $dateOccurred)
    {
        $this->applyAndRecordThat(new UserUpdated($this->userId, $email, $status, $role, $dateOccurred));
    }

    // set user status to deleted
    public function delete(Date $dateOccurred)
    {
        $this->applyAndRecordThat(new UserDeleted($this->userId, UserStatus::getDeleted(), $dateOccurred));
    }

    // apply a password reset token to user
    public function applyPasswordResetToken(PasswordResetToken $token, Date $dateOccurred)
    {
        $this->applyAndRecordThat(new PasswordResetTokenApplied($this->userId, $token, $dateOccurred));
    }

    protected function applyUserRegistered(UserRegistered $event)
    {
        $this->userId = $event->getAggregateId();
        $this->email = $event->getEmail();
        $this->passwordHash = $event->getPasswordHash();
        $this->status = $event->getStatus();
        $this->role = $event->getRole();
    }

    protected function applyUserActivated(UserActivated $event)
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

    protected function applyPasswordResetTokenApplied(PasswordResetTokenApplied $event)
    {
        $this->passwordResetToken = $event->getPasswordResetToken();
    }

    protected function applyUserDeleted(UserDeleted $event)
    {
        $this->status = $event->getStatus();
    }
}
