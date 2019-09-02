<?php

namespace Tailgate\Test\Application\Validator\User;

use PHPUnit\Framework\TestCase;
use Tailgate\Application\Validator\User\UniqueEmail;
use Tailgate\Application\Validator\User\UniqueEmailException;
use Tailgate\Domain\Model\User\UserViewRepositoryInterface;

class UniqueEmailTest extends TestCase
{
    public function testItReturnsTrueWhenEmailDoesNotExist()
    {
        $userViewRepository = $this->createMock(UserViewRepositoryInterface::class);

        $userViewRepository->expects($this->once())->method('byEmail')->willReturn(false);

        $validator = new UniqueEmail($userViewRepository);
        $this->assertTrue($validator->validate('emailNotExist@email.com'));
    }

    public function testItReturnsFalseWhenEmailExists()
    {
        $input = 'email@email.com';

        $userViewRepository = $this->createMock(UserViewRepositoryInterface::class);

        $userViewRepository->expects($this->once())->method('byEmail')->willReturn(true);

        $validator = new UniqueEmail($userViewRepository);
        $this->assertFalse($validator->validate($input));
    }
}
