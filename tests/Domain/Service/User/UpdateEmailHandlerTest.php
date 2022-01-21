<?php

namespace Tailgate\Test\Domain\Service\User;

use Tailgate\Application\Command\User\UpdateEmailCommand;
use Tailgate\Application\Validator\ValidatorInterface;
use Tailgate\Domain\Model\Common\Date;
use Tailgate\Domain\Model\Common\Email;
use Tailgate\Domain\Model\User\EmailUpdated;
use Tailgate\Domain\Model\User\User;
use Tailgate\Domain\Model\User\UserId;
use Tailgate\Domain\Model\User\UserRepositoryInterface;
use Tailgate\Domain\Service\Clock\FakeClock;
use Tailgate\Domain\Service\User\UpdateEmailHandler;
use Tailgate\Test\BaseTestCase;

class UpdateEmailHandlerTest extends BaseTestCase
{
    public function setUp(): void
    {
        $this->userId = UserId::fromString('userId');
        $this->passwordHash = 'password';
        $this->email = Email::fromString('email@email.com');
        $this->dateOccurred = Date::fromDateTimeImmutable($this->getFakeTime()->currentTime());
        $this->newEmail = Email::fromString('newEmail@email.com');

        // create a user and clear events
        $this->user = User::register(
            $this->userId,
            $this->email,
            $this->passwordHash,
            $this->dateOccurred
        );
        $this->user->clearRecordedEvents();

        $this->updateEmailCommand = new UpdateEmailCommand($this->userId, $this->newEmail);
    }

    public function testItAddsAEmailUpdatedRepository()
    {
        $validator = $this->createMock(ValidatorInterface::class);
        $validator->expects($this->once())->method('assert')->willReturn(true);

        $userRepository = $this->getMockBuilder(UserRepositoryInterface::class)->getMock();
        $userRepository->expects($this->once())->method('get')->willReturn($this->user);
        $userRepository->expects($this->once())->method('add');

        $updateEmailHandler = new UpdateEmailHandler($validator, new FakeClock(), $userRepository);

        $updateEmailHandler->handle($this->updateEmailCommand);
    }
}
