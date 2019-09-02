<?php

namespace Tailgate\Domain\Model\User;

use Buttercup\Protects\DomainEvent;

class UserUpdated implements DomainEvent, UserDomainEvent
{
    private $userId;
    private $email;
    private $status;
    private $role;
    private $occurredOn;

    public function __construct(
        UserId $userId,
        $email,
        $status,
        $role
    ) {
        $this->userId = $userId;
        $this->email = $email;
        $this->status = $status;
        $this->role = $role;
        $this->occurredOn = new \DateTimeImmutable();
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

    public function getOccurredOn()
    {
        return $this->occurredOn;
    }
}
