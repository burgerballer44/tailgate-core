<?php

namespace Tailgate\Test\Domain\Service\User;

use Tailgate\Application\Command\User\ActivateUserCommand;
use Tailgate\Application\Validator\ValidatorInterface;
use Tailgate\Domain\Model\Common\Date;
use Tailgate\Domain\Model\Common\Email;
use Tailgate\Domain\Model\User\User;
use Tailgate\Domain\Model\User\UserActivated;
use Tailgate\Domain\Model\User\UserId;
use Tailgate\Domain\Model\User\UserRepositoryInterface;
use Tailgate\Domain\Model\User\UserStatus;
use Tailgate\Domain\Service\Clock\FakeClock;
use Tailgate\Domain\Service\User\ActivateUserHandler;
use Tailgate\Test\BaseTestCase;

class ActivateUserHandlerTest extends BaseTestCase
{
    public function setUp(): void
    {
        $this->userId = UserId::fromString('userId');
        $this->passwordHash = 'password';
        $this->email = Email::fromString('email@email.com');
        $this->dateOccurred = Date::fromDateTimeImmutable($this->getFakeTime()->currentTime());

        // create a user and clear events
        $this->user = User::register(
            $this->userId,
            $this->email,
            $this->passwordHash,
            $this->dateOccurred
        );
        $this->user->clearRecordedEvents();

        $this->activateUserCommand = new ActivateUserCommand($this->userId);
    }

    public function testItAddsAUserActivatedToTheRepository()
    {
        $validator = $this->createMock(ValidatorInterface::class);
        $validator->expects($this->once())->method('assert')->willReturn(true);

        $userRepository = $this->getMockBuilder(UserRepositoryInterface::class)->getMock();
        $userRepository->expects($this->once())->method('get')->willReturn($this->user);
        $userRepository->expects($this->once())->method('add');

        $activateUserHandler = new ActivateUserHandler($validator, new FakeClock(), $userRepository);

        $activateUserHandler->handle($this->activateUserCommand);
    }
}
