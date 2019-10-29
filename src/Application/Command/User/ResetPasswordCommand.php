<?php

namespace Tailgate\Application\Command\User;

class ResetPasswordCommand
{
    private $userId;
    private $password;
    private $confirmPassword;

    public function __construct(string $userId, string $password, string $confirmPassword)
    {
        $this->userId = $userId;
        $this->password = $password;
        $this->confirmPassword = $confirmPassword;
    }

    public function getUserId()
    {
        return $this->userId;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getConfirmPassword()
    {
        return $this->confirmPassword;
    }
}
