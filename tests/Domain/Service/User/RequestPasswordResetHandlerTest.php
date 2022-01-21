<?php

namespace Tailgate\Test\Domain\Service\User;

use Tailgate\Application\Command\User\RequestPasswordResetCommand;
use Tailgate\Application\Validator\ValidatorInterface;
use Tailgate\Domain\Model\Common\Date;
use Tailgate\Domain\Model\Common\Email;
use Tailgate\Domain\Model\User\PasswordResetTokenApplied;
use Tailgate\Domain\Model\User\User;
use Tailgate\Domain\Model\User\UserId;
use Tailgate\Domain\Model\User\UserRepositoryInterface;
use Tailgate\Domain\Service\Clock\FakeClock;
use Tailgate\Domain\Service\User\RequestPasswordResetHandler;
use Tailgate\Test\BaseTestCase;

class RequestPasswordResetHandlerTest extends BaseTestCase
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

        $this->requestPasswordReset = new RequestPasswordResetCommand($this->userId);
    }

    public function testItAddsAPasswordResetTokenAppliedToTheRepository()
    {
        $validator = $this->createMock(ValidatorInterface::class);
        $validator->expects($this->once())->method('assert')->willReturn(true);

        $userRepository = $this->getMockBuilder(UserRepositoryInterface::class)->getMock();
        $userRepository->expects($this->once())->method('get')->willReturn($this->user);
        $userRepository->expects($this->once())->method('add');

        $requestPasswordResethanlder = new RequestPasswordResetHandler($validator, new FakeClock(), $userRepository);

        $requestPasswordResethanlder->handle($this->requestPasswordReset);
    }
}
