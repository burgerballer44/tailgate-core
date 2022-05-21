<?php

namespace Tailgate\Test\Domain\Service\User;

use Tailgate\Application\Command\User\RegisterUserCommand;
use Tailgate\Domain\Model\Common\Date;
use Tailgate\Domain\Model\Common\Email;
use Tailgate\Domain\Model\User\UserId;
use Tailgate\Domain\Model\User\UserRepositoryInterface;
use Tailgate\Domain\Model\User\UserRole;
use Tailgate\Domain\Model\User\UserStatus;
use Tailgate\Domain\Service\Clock\FakeClock;
use Tailgate\Domain\Service\PasswordHashing\PasswordHashingInterface;
use Tailgate\Domain\Service\User\RegisterUserHandler;
use Tailgate\Test\BaseTestCase;

class RegisterUserHandlerTest extends BaseTestCase
{
    public function setUp(): void
    {
        $this->email = Email::fromString('email@email.com');
        $this->passwordHash = 'password';
        $this->confirmPassword = 'password';
        $this->dateOccurred = Date::fromDateTimeImmutable($this->getFakeTime()->currentTime());

        $this->registerUserCommand = new RegisterUserCommand(
            $this->email,
            $this->passwordHash,
            $this->confirmPassword
        );
    }

    public function testItAddsAUserRegisteredToTheUserRepository()
    {
        $userRepository = $this->getMockBuilder(UserRepositoryInterface::class)->getMock();
        $userRepository->expects($this->once())->method('nextIdentity')->willReturn(new UserId());
        $userRepository->expects($this->once())->method('add');

        $passwordHashing = $this->createMock(PasswordHashingInterface::class);
        $passwordHashing->expects($this->once())->method('hash')->willReturn($this->passwordHash);

        $registerUserHandler = new RegisterUserHandler(new FakeClock(), $userRepository, $passwordHashing);

        $user = $registerUserHandler->handle($this->registerUserCommand);

        $this->assertNotEmpty($user['userId']);
        $this->assertEquals($this->email, $user['email']);
        $this->assertEquals(UserStatus::getPending(), $user['status']);
        $this->assertEquals(UserRole::getStandard(), $user['role']);
    }
}
