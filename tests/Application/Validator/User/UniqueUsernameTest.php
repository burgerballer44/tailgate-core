<?php

namespace Tailgate\Test\Application\Validator\User;

use PHPUnit\Framework\TestCase;
use Tailgate\Application\Validator\User\UniqueUsername;
use Tailgate\Application\Validator\User\UniqueUsernameException;
use Tailgate\Infrastructure\Persistence\ViewRepository\PDO\PDOUserViewRepository;

class UniqueUsernameTest extends TestCase
{
    public function testItReturnsTrueWhenUserNameDoesNotExist()
    {
        // only needs the byUsername method
        $userViewRepository = $this->getMockBuilder(PDOUserViewRepository::class)
            ->disableOriginalConstructor()
            ->setMethods(['byUsername'])
            ->getMock();

        $userViewRepository
            ->expects($this->once())
            ->method('byUsername')
            ->willReturn(false);

        $validator = new UniqueUsername($userViewRepository);
        $this->assertTrue($validator->validate('burger1'));
    }

    public function testItReturnsFalseWhenUserNameExists()
    {
        $input = 'burger';

        // only needs the byUsername method
        $userViewRepository = $this->getMockBuilder(PDOUserViewRepository::class)
            ->disableOriginalConstructor()
            ->setMethods(['byUsername'])
            ->getMock();

        $userViewRepository
            ->expects($this->once())
            ->method('byUsername')
            ->willReturn(true);

        $validator = new UniqueUsername($userViewRepository);
        $this->assertFalse($validator->validate($input));
    }
}
