<?php

namespace Tailgate\Domain\Model\User;

use Buttercup\Protects\DomainEvent;

class UserActivated implements DomainEvent, UserDomainEvent
{
    private $userId;
    private $status;
    private $occurredOn;

    public function __construct(UserId $userId, $status)
    {
        $this->userId = $userId;
        $this->status = $status;
        $this->occurredOn = new \DateTimeImmutable();
    }

    public function getAggregateId()
    {
        return $this->userId;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function getOccurredOn()
    {
        return $this->occurredOn;
    }
}
