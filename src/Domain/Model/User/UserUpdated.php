<?php

namespace Tailgate\Domain\Model\User;

use Tailgate\Domain\Model\Common\Date;
use Tailgate\Domain\Model\Common\Email;
use Tailgate\Domain\Model\DomainEvent;

class UserUpdated implements DomainEvent, UserDomainEvent
{
    private $userId;
    private $email;
    private $status;
    private $role;
    private $dateOccurred;

    public function __construct(
        UserId $userId,
        Email $email,
        UserStatus $status,
        UserRole $role,
        Date $dateOccurred
    ) {
        $this->userId = $userId;
        $this->email = $email;
        $this->status = $status;
        $this->role = $role;
        $this->dateOccurred = $dateOccurred;
    }

    public function getEventDescription(): string
    {
        return 'User information updated.';
    }

    public function getAggregateId()
    {
        return $this->userId;
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
