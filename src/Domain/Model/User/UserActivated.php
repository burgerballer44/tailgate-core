<?php

namespace Tailgate\Domain\Model\User;

use Tailgate\Domain\Model\Common\Date;
use Tailgate\Domain\Model\DomainEvent;

class UserActivated implements DomainEvent, UserDomainEvent
{
    private $userId;
    private $status;
    private $dateOccurred;

    public function __construct(UserId $userId, UserStatus $status, Date $dateOccurred)
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
