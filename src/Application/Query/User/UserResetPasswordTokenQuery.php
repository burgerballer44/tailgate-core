<?php

namespace Tailgate\Application\Query\User;

class UserResetPasswordTokenQuery
{
    private $passwordResetToken;

    public function __construct($passwordResetToken)
    {
        $this->passwordResetToken = $passwordResetToken;
    }

    public function getPasswordResetToken()
    {
        return $this->passwordResetToken;
    }
}
