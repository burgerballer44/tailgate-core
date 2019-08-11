<?php

namespace Tailgate\Domain\Model\User;

use Buttercup\Protects\DomainEvent;

class EmailUpdated implements DomainEvent
{
    private $userId;
    private $email;
    private $occurredOn;

    public function __construct(UserId $userId, $email) {
        $this->userId = $userId;
        $this->email = $email;
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

    public function getOccurredOn()
    {
        return $this->occurredOn;
    }
}