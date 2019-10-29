<?php

namespace Tailgate\Domain\Model\User;

use Buttercup\Protects\DomainEvent;

class PasswordResetTokenCreated implements DomainEvent, UserDomainEvent
{
    private $userId;
    private $passwordResetToken;
    private $occurredOn;

    public function __construct(UserId $userId, $passwordResetToken)
    {
        $this->userId = $userId;
        $this->passwordResetToken = $passwordResetToken;
        $this->occurredOn = new \DateTimeImmutable();
    }

    public function getAggregateId()
    {
        return $this->userId;
    }

    public function getPasswordResetToken()
    {
        return $this->passwordResetToken;
    }

    public function getOccurredOn()
    {
        return $this->occurredOn;
    }
}
