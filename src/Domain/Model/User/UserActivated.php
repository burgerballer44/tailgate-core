<?php

namespace Tailgate\Domain\Model\User;

use Tailgate\Domain\Model\DomainEvent;

class UserActivated implements DomainEvent, UserDomainEvent
{
    private $userId;
    private $status;
    private $dateOccurred;

    public function __construct(UserId $userId, $status, $dateOccurred)
    {
        $this->userId = $userId;
        $this->status = $status;
        $this->dateOccurred = $dateOccurred;
    }

    public function getEventDescription(): string
    {
        return 'User activated.';
    }

    public function getAggregateId()
    {
        return $this->userId;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function getDateOccurred()
    {
        return (string) $this->dateOccurred;
    }
}
