<?php

namespace Tailgate\Test\Domain\Service\User;

use Tailgate\Application\Command\User\UpdateUserCommand;
use Tailgate\Application\Validator\ValidatorInterface;
use Tailgate\Domain\Model\Common\Date;
use Tailgate\Domain\Model\Common\Email;
use Tailgate\Domain\Model\User\User;
use Tailgate\Domain\Model\User\UserId;
use Tailgate\Domain\Model\User\UserRepositoryInterface;
use Tailgate\Domain\Model\User\UserRole;
use Tailgate\Domain\Model\User\UserStatus;
use Tailgate\Domain\Model\User\UserUpdated;
use Tailgate\Domain\Service\Clock\FakeClock;
use Tailgate\Domain\Service\User\UpdateUserHandler;
use Tailgate\Test\BaseTestCase;

class UpdateUserHandlerTest extends BaseTestCase
{
    public function setUp(): void
    {
        $this->userId = UserId::fromString('userId');
        $this->passwordHash = 'password';
        $this->email = Email::fromString('email@email.com');
        $this->dateOccurred = Date::fromDateTimeImmutable($this->getFakeTime()->currentTime());
        $this->newEmail = Email::fromString('updated@email.com');
        $this->status = UserStatus::getPending();
        $this->role = UserRole::getAdmin();

        // create a user and clear events
        $this->user = User::register(
            $this->userId,
            $this->email,
            $this->passwordHash,
            $this->dateOccurred
        );
        $this->user->clearRecordedEvents();

        $this->updateUserCommand = new UpdateUserCommand(
            $this->userId,
            $this->newEmail,
            $this->status,
            $this->role
        );
    }

    public function testItAddsAUserUpdatedToTheUserRepository()
    {
        $validator = $this->createMock(ValidatorInterface::class);
        $validator->expects($this->once())->method('assert')->willReturn(true);

        $userRepository = $this->getMockBuilder(UserRepositoryInterface::class)->getMock();
        $userRepository->expects($this->once())->method('get')->willReturn($this->user);

        $userRepository->expects($this->once())->method('add');

        $updateUserHandler = new UpdateUserHandler($validator, new FakeClock(), $userRepository);

        $updateUserHandler->handle($this->updateUserCommand);
    }
}
