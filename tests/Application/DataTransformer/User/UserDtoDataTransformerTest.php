<?php

namespace Tailgate\Test\Application\DataTransformer\User;

use PHPUnit\Framework\TestCase;
use Tailgate\Domain\Model\User\User;
use Tailgate\Domain\Model\User\UserId;
use Tailgate\Application\DataTransformer\User\UserDtoDataTransformer;

class UserDtoDataTransformerTest extends TestCase
{
    public function testItTurnsAUserIntoAnArray()
    {
        $id = new UserId;
        $username = 'username';
        $password = 'password';
        $email = 'email@email.com';

        $user = User::create($id, $username, $password, $email);
        $transformer = new UserDtoDataTransformer();

        $transformer->write($user);

        $this->assertEquals(
            [
                'id' => (string) $id,
                'username' => $username,
                'email' => $email,
            ],
            $transformer->read()         
        );
    }
}
