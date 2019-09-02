<?php

namespace Tailgate\Test\Application\Validator\User;

use PHPUnit\Framework\TestCase;
use Tailgate\Application\Validator\Player\UniqueUsername;
use Tailgate\Application\Validator\Player\UniqueUsernameException;
use Tailgate\Domain\Model\Group\PlayerViewRepositoryInterface;

class UniqueUsernameTest extends TestCase
{
    public function testItReturnsTrueWhenUserNameDoesNotExist()
    {
        $playerViewRepository = $this->createMock(PlayerViewRepositoryInterface::class);

        $playerViewRepository->expects($this->once())->method('byUsername')->willReturn(false);

        $validator = new UniqueUsername($playerViewRepository);
        $this->assertTrue($validator->validate('burger1'));
    }

    public function testItReturnsFalseWhenUserNameExists()
    {
        $input = 'burger';

        $playerViewRepository = $this->createMock(PlayerViewRepositoryInterface::class);

        $playerViewRepository->expects($this->once())->method('byUsername')->willReturn(true);

        $validator = new UniqueUsername($playerViewRepository);
        $this->assertFalse($validator->validate($input));
    }
}
