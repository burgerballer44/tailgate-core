<?php

namespace Tailgate\Test\Domain\Service\User;

use Tailgate\Application\Command\User\DeleteUserCommand;
use Tailgate\Domain\Model\Common\Date;
use Tailgate\Domain\Model\Common\Email;
use Tailgate\Domain\Model\User\User;
use Tailgate\Domain\Model\User\UserId;
use Tailgate\Domain\Model\User\UserRepositoryInterface;
use Tailgate\Domain\Service\User\DeleteUserHandler;
use Tailgate\Infrastructure\Service\Clock\FakeClock;
use Tailgate\Test\BaseTestCase;

class DeleteUserHandlerTest extends BaseTestCase
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

        $this->deleteUserCommand = new DeleteUserCommand($this->userId);
    }

    public function testItAddsAUserDeletedToTheRepository()
    {
        $userRepository = $this->getMockBuilder(UserRepositoryInterface::class)->getMock();
        $userRepository->expects($this->once())->method('get')->willReturn($this->user);
        $userRepository->expects($this->once())->method('add');

        $deleteUserHandler = new DeleteUserHandler($userRepository, new FakeClock());

        $deleteUserHandler->handle($this->deleteUserCommand);
    }
}
