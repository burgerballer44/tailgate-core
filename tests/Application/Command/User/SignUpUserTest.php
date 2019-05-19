<?php

namespace Tailgate\Test\Application\Command\User;

use PHPUnit\Framework\TestCase;
use Tailgate\Application\Command\User\SignUpUserCommand;

class SignUpUserTest extends TestCase
{
    public function testItCapturesCertainFields()
    {
        $username = 'username';
        $password = 'password';
        $email = 'email@email.com';

        $command = new SignUpUserCommand($username, $password, $email);

        $this->assertEquals($username, $command->getUsername());
        $this->assertEquals($password, $command->getPassword());
        $this->assertEquals($email, $command->getEmail());
    }
}
