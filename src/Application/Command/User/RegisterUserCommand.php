<?php

namespace Tailgate\Application\Command\User;

class RegisterUserCommand
{
    private $email;
    private $password;
    private $confirmPassword;

    public function __construct($email, $password, $confirmPassword)
    {
        $this->email = $email;
        $this->password = $password;
        $this->confirmPassword = $confirmPassword;
    }

    public function getEmail()
    {
        return $this->email;
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
