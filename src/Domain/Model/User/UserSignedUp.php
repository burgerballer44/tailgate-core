<?php

namespace Tailgate\Domain\Model\User;

use Buttercup\Protects\DomainEvent;

class UserSignedUp implements DomainEvent
{
    private $userId;
    private $username;
    private $passwordHash;
    private $email;
    private $status;
    private $role;
    private $key;
    private $occurredOn;

    public function __construct(
        UserId $userId,
        $username,
        $passwordHash,
        $email,
        $status,
        $role,
        $key
    ) {
        $this->userId = $userId;
        $this->username = $username;
        $this->passwordHash = $passwordHash;
        $this->email = $email;
        $this->status = $status;
        $this->role = $role;
        $this->key = $key;
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

    public function getKey()
    {
        return $this->key;
    }

    public function getOccurredOn()
    {
        return $this->occurredOn;
    }
}