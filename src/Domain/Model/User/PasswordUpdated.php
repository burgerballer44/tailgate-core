<?php

namespace Tailgate\Domain\Model\User;

use Buttercup\Protects\DomainEvent;

class PasswordUpdated implements DomainEvent, UserDomainEvent
{
    private $userId;
    private $passwordHash;
    private $occurredOn;

    public function __construct(UserId $userId, $passwordHash)
    {
        $this->userId = $userId;
        $this->passwordHash = $passwordHash;
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

    public function getOccurredOn()
    {
        return $this->occurredOn;
    }
}
