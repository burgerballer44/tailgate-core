<?php

namespace Tailgate\Application\Command\User;

class SignUpUserCommand
{
    private $username;
    private $password;
    private $email;
    private $confirmPassword;

    public function __construct(string $username, string $password, string $email, string $confirmPassword)
    {
        $this->username = $username;
        $this->password = $password;
        $this->email = $email;
        $this->confirmPassword = $confirmPassword;
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
    
    public function getConfirmPassword()
    {
        return $this->confirmPassword;
    }
}