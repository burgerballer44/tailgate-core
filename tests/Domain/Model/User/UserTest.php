<?php

namespace Tailgate\Test\Domain\Model\User;

use PHPUnit\Framework\TestCase;
use Tailgate\Domain\Model\User\User;
use Tailgate\Domain\Model\User\UserId;

class UserDtoDataTransformerTest extends TestCase
{
    public function testItIsInitializable()
    {
        $id = new UserId;
        $username = 'username';
        $password = 'password';
        $email = 'email@email.com';

        $user = User::create($id, $username, $password, $email);

        $this->assertInstanceOf(User::class, $user);
    }
}
