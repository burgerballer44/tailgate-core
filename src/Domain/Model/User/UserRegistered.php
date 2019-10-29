<?php

namespace Tailgate\Domain\Model\User;

use Buttercup\Protects\DomainEvent;

class UserRegistered implements DomainEvent, UserDomainEvent
{
    private $userId;
    private $passwordHash;
    private $email;
    private $status;
    private $role;
    private $passwordResetToken;
    private $occurredOn;

    public function __construct(
        UserId $userId,
        $email,
        $passwordHash,
        $status,
        $role,
        $passwordResetToken
    ) {
        $this->userId = $userId;
        $this->email = $email;
        $this->passwordHash = $passwordHash;
        $this->status = $status;
        $this->role = $role;
        $this->passwordResetToken = $passwordResetToken;
        $this->occurredOn = new \DateTimeImmutable();
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

    public function getPasswordResetToken()
    {
        return $this->passwordResetToken;
    }

    public function getOccurredOn()
    {
        return $this->occurredOn;
    }
}
