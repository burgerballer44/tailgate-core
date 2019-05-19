<?php

namespace Tailgate\Test\Domain\Model\User;

use PHPUnit\Framework\TestCase;
use Tailgate\Domain\Model\User\UserSignedUp;
use Tailgate\Domain\Model\User\UserId;

class UserSignedUpTest extends TestCase
{
    public function testItExposesCertainFields()
    {
        $id = new UserId('someUserId');
        $username = 'username';
        $password = 'password';
        $email = 'email@email.com';

        $event = new UserSignedUp($id, $username, $password, $email);

        $this->assertTrue($event->getAggregateId()->equals($id));
        $this->assertEquals($username, $event->getUsername());
        $this->assertEquals($password, $event->getPassword());
        $this->assertEquals($email, $event->getEmail());
    }
}
