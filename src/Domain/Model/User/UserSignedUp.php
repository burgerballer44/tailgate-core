<?php

namespace Tailgate\Domain\Model\User;

use Buttercup\Protects\DomainEvent;

class UserSignedUp implements DomainEvent
{
    private $userId;
    private $username;
    private $password;
    private $email;
    private $occuredOn;

    public function __construct(UserId $userId, $username, $password, $email)
    {
        $this->userId = $userId;
        $this->username = $username;
        $this->password = $password;
        $this->email = $email;
        $this->occurredOn = new \DateTimeImmutable();
    }

    public function getAggregateId()
    {
        return $this->userId;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getOccuredOn()
    {
        return $this->occurredOn;
    }
}