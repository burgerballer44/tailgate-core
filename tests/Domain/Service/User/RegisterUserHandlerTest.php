<?php

namespace Tailgate\Test\Domain\Service\User;

use PHPUnit\Framework\TestCase;
use Tailgate\Application\Command\User\RegisterUserCommand;
use Tailgate\Application\Validator\ValidatorInterface;
use Tailgate\Domain\Service\PasswordHashing\PasswordHashingInterface;
use Tailgate\Domain\Model\User\User;
use Tailgate\Domain\Model\User\UserId;
use Tailgate\Domain\Model\User\UserRegistered;
use Tailgate\Domain\Model\User\UserRepositoryInterface;
use Tailgate\Domain\Service\User\RegisterUserHandler;

class RegisterUserHandlerTest extends TestCase
{
    private $password = 'password';
    private $confirmPassword = 'password';
    private $email = 'email@email.com';
    private $passwordResetToken = '';
    private $registerUserCommand;

    public function setUp()
    {
        $this->registerUserCommand = new RegisterUserCommand(
            $this->email,
            $this->password,
            $this->confirmPassword
        );
    }

    public function testItAddsAUserRegisteredToTheUserRepository()
    {
        $email = $this->email;
        $password = $this->password;
        $confirmPassword = $this->confirmPassword;
        $passwordResetToken = $this->passwordResetToken;

        $userRepository = $this->getMockBuilder(UserRepositoryInterface::class)->getMock();

        // the nextIdentity method should be called once and will return a new UserID
        $userRepository->expects($this->once())->method('nextIdentity')->willReturn(new UserId());

        // the add method should be called once
        // the user object should have the UserRegistered event
        $userRepository->expects($this->once())->method('add')->with($this->callback(
            function ($user) use ($password, $confirmPassword, $email, $passwordResetToken) {
                $events = $user->getRecordedEvents();

                return $events[0] instanceof UserRegistered
                && $events[0]->getAggregateId() instanceof UserId
                && $events[0]->getEmail() === $email
                && $events[0]->getPasswordHash() === $password
                && $events[0]->getStatus() === User::STATUS_PENDING
                && $events[0]->getRole() === User::ROLE_USER
                && $events[0]->getPasswordResetToken() === $passwordResetToken
                && $events[0]->getOccurredOn() instanceof \DateTimeImmutable;
            }
        ));

        $validator = $this->createMock(ValidatorInterface::class);
        $validator->expects($this->once())->method('assert')->willReturn(true);

        $passwordHashing = $this->createMock(PasswordHashingInterface::class);
        $passwordHashing->expects($this->once())->method('hash')->willReturn($password);

        $registerUserHandler = new RegisterUserHandler($validator, $userRepository, $passwordHashing);

        $user = $registerUserHandler->handle($this->registerUserCommand);

        $this->assertNotEmpty($user['userId']);
        $this->assertEquals($this->email, $user['email']);
        $this->assertEquals(User::STATUS_PENDING, $user['status']);
        $this->assertEquals(User::ROLE_USER, $user['role']);
    }
}
