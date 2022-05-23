<?php

namespace Tailgate\Test\Domain\Service\User;

use Tailgate\Application\Command\User\ResetPasswordCommand;
use Tailgate\Domain\Model\Common\Date;
use Tailgate\Domain\Model\Common\Email;
use Tailgate\Domain\Model\User\User;
use Tailgate\Domain\Model\User\UserId;
use Tailgate\Domain\Model\User\UserRepositoryInterface;
use Tailgate\Domain\Service\PasswordHashing\PasswordHashingInterface;
use Tailgate\Domain\Service\User\ResetPasswordHandler;
use Tailgate\Infrastructure\Service\Clock\FakeClock;
use Tailgate\Test\BaseTestCase;

class ResetPasswordHandlerTest extends BaseTestCase
{
    public function setUp(): void
    {
        $this->userId = UserId::fromString('userId');
        $this->passwordHash = 'password';
        $this->email = Email::fromString('email@email.com');
        $this->dateOccurred = Date::fromDateTimeImmutable($this->getFakeTime()->currentTime());
        $this->newPasswordHash = 'newPassword';
        $this->newPasswordHashConfirm = 'newPassword';

        // create a user and clear events
        $this->user = User::register(
            $this->userId,
            $this->email,
            $this->passwordHash,
            $this->dateOccurred
        );
        $this->user->clearRecordedEvents();

        $this->resetPasswordCommand = new ResetPasswordCommand($this->userId, $this->newPasswordHash, $this->newPasswordHashConfirm);
    }

    public function testItAddsAPasswordUpdatedRepository()
    {
        $userRepository = $this->getMockBuilder(UserRepositoryInterface::class)->getMock();
        $userRepository->expects($this->once())->method('get')->willReturn($this->user);
        $userRepository->expects($this->once())->method('add');

        $passwordHashing = $this->createMock(PasswordHashingInterface::class);
        $passwordHashing->expects($this->once())->method('hash')->willReturn($this->newPasswordHash);

        $resetPasswordHandler = new ResetPasswordHandler(
            new FakeClock(),
            $userRepository,
            $passwordHashing
        );

        $resetPasswordHandler->handle($this->resetPasswordCommand);
    }
}
