<?php

namespace Tailgate\Test\Application\Command\User;

use PHPUnit\Framework\TestCase;
use Tailgate\Application\Command\User\SignUpUserCommand;
use Tailgate\Application\Command\User\SignUpUserHandler;
use Tailgate\Domain\Model\User\User;
use Tailgate\Domain\Model\User\UserId;
use Tailgate\Domain\Model\User\UserSignedUp;
use Tailgate\Common\PasswordHashing\PasswordHashingInterface;
use Tailgate\Infrastructure\Persistence\Repository\UserRepository;

class SignUpUserHandlerTest extends TestCase
{
    private $username = 'username';
    private $password = 'password';
    private $confirmPassword = 'password';
    private $email = 'email@email.com';
    private $signUpUserCommand;

    public function setUp()
    {
        $this->signUpUserCommand = new SignUpUserCommand(
            $this->username,
            $this->password,
            $this->email,
            $this->confirmPassword
        );
    }

    public function testItAddsAUserSignedUpToTheUserRepository()
    {
        $username = $this->username;
        $password = $this->password;
        $confirmPassword = $this->confirmPassword;
        $email = $this->email;

        // only needs the add method
        $userRepository = $this->getMockBuilder(UserRepository::class)
            ->disableOriginalConstructor()
            ->setMethods(['add'])
            ->getMock();

        // the add method should be called once
        // the user object should have the UserSignedUp event
        $userRepository
            ->expects($this->once())
            ->method('add')
            ->with($this->callback(function($user) use (
                $username,
                $password,
                $confirmPassword,
                $email
            ) {
                $events = $user->getRecordedEvents();

                return $events[0] instanceof UserSignedUp
                && $events[0]->getAggregateId() instanceof UserId
                && $events[0]->getUsername() === $username
                && $events[0]->getPasswordHash() === $password
                && $events[0]->getEmail() === $email
                && $events[0]->getStatus() === User::STATUS_PENDING
                && $events[0]->getRole() === User::ROLE_USER
                && $events[0]->getOccurredOn() instanceof \DateTimeImmutable;
            }
        ));

        $passwordHashing = $this->createMock(PasswordHashingInterface::class);
        $passwordHashing
            ->expects($this->once())
            ->method('hash')
            ->willReturn($password);

        $signUpUserHandler = new SignUpUserHandler(
            $userRepository,
            $passwordHashing
        );

        $signUpUserHandler->handle($this->signUpUserCommand);
    }
}
