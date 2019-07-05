<?php

namespace Tailgate\Test\Application\Validator\User;

use PHPUnit\Framework\TestCase;
use Tailgate\Application\Validator\User\UniqueEmail;
use Tailgate\Application\Validator\User\UniqueEmailException;
use Tailgate\Infrastructure\Persistence\ViewRepository\PDO\PDOUserViewRepository;
use Tailgate\Tests\DatabaseTestCase;

class UniqueEmailTest extends DatabaseTestCase
{
    public function testItReturnsTrueWhenUserNameDoesNotExist()
    {
        // only needs the byEmail method
        $userViewRepository = $this->getMockBuilder(PDOUserViewRepository::class)
            ->disableOriginalConstructor()
            ->setMethods(['byEmail'])
            ->getMock();

        $userViewRepository
            ->expects($this->once())
            ->method('byEmail')
            ->willReturn(false);

        $validator = new UniqueEmail($userViewRepository);
        $this->assertTrue($validator->validate('emailNotExist@email.com'));
    }

    public function testItReturnsFalseWhenUserNameExists()
    {
        $input = 'email@email.com';

        // only needs the byEmail method
        $userViewRepository = $this->getMockBuilder(PDOUserViewRepository::class)
            ->disableOriginalConstructor()
            ->setMethods(['byEmail'])
            ->getMock();

        $userViewRepository
            ->expects($this->once())
            ->method('byEmail')
            ->willReturn(true);

        $validator = new UniqueEmail($userViewRepository);
        $this->assertFalse($validator->validate($input));
    }
}