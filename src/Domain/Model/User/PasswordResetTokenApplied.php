<?php

namespace Tailgate\Domain\Model\User;

use Tailgate\Domain\Model\DomainEvent;

class PasswordResetTokenApplied implements DomainEvent, UserDomainEvent
{
    private $userId;
    private $passwordResetToken;
    private $dateOccurred;

    public function __construct(UserId $userId, $passwordResetToken, $dateOccurred)
    {
        $this->userId = $userId;
        $this->passwordResetToken = $passwordResetToken;
        $this->dateOccurred = $dateOccurred;
    }

    public function getEventDescription(): string
    {
        return 'Password reset token applied.';
    }

    public function getAggregateId()
    {
        return $this->userId;
    }

    public function getPasswordResetToken()
    {
        return $this->passwordResetToken;
    }

    public function getDateOccurred()
    {
        return (string) $this->dateOccurred;
    }
}
