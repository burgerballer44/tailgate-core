<?php

namespace Tailgate\Domain\Model\User;

use Tailgate\Domain\Model\Common\Date;
use Tailgate\Domain\Model\Common\Email;
use Tailgate\Domain\Model\DomainEvent;

class EmailUpdated implements DomainEvent, UserDomainEvent
{
    private $userId;
    private $email;
    private $dateOccurred;

    public function __construct(UserId $userId, Email $email, Date $dateOccurred)
    {
        $this->userId = $userId;
        $this->email = $email;
        $this->dateOccurred = $dateOccurred;
    }

    public function getEventDescription(): string
    {
        return 'User email updated.';
    }

    public function getAggregateId()
    {
        return $this->userId;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getDateOccurred()
    {
        return (string) $this->dateOccurred;
    }
}
