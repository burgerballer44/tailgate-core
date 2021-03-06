<?php

namespace Tailgate\Domain\Model\User;

use Tailgate\Domain\Model\Common\Date;
use Tailgate\Domain\Model\Common\Email;
use Tailgate\Domain\Model\DomainEvent;

class UserRegistered implements DomainEvent, UserDomainEvent
{
    private $userId;
    private $passwordHash;
    private $email;
    private $status;
    private $role;
    private $dateOccurred;

    public function __construct(
        UserId $userId,
        Email $email,
        $passwordHash,
        UserStatus $status,
        UserRole $role,
        Date $dateOccurred
    ) {
        $this->userId = $userId;
        $this->email = $email;
        $this->passwordHash = $passwordHash;
        $this->status = $status;
        $this->role = $role;
        $this->dateOccurred = $dateOccurred;
    }

    public function getEventDescription(): string
    {
        return 'User added to the application.';
    }

    public function getAggregateId()
    {
        return $this->userId;
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

    public function getDateOccurred()
    {
        return (string) $this->dateOccurred;
    }
}
