<?php

namespace Tailgate\Domain\Model\User;

use Tailgate\Domain\Model\DomainEvent;

class PasswordUpdated implements DomainEvent, UserDomainEvent
{
    private $userId;
    private $passwordHash;
    private $dateOccurred;

    public function __construct(UserId $userId, $passwordHash, $dateOccurred)
    {
        $this->userId = $userId;
        $this->passwordHash = $passwordHash;
        $this->dateOccurred = $dateOccurred;
    }

    public function getEventDescription() : string
    {
        return 'Password updated.';
    }

    public function getAggregateId()
    {
        return $this->userId;
    }

    public function getPasswordHash()
    {
        return $this->passwordHash;
    }

    public function getDateOccurred()
    {
        return (string) $this->dateOccurred;
    }
}
