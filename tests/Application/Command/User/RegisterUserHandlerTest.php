<?php

namespace Tailgate\Test\Application\Command\User;

use PHPUnit\Framework\TestCase;
use Tailgate\Application\Command\User\RegisterUserCommand;
use Tailgate\Application\Command\User\RegisterUserHandler;
use Tailgate\Domain\Model\User\User;
use Tailgate\Domain\Model\User\UserId;
use Tailgate\Domain\Model\User\UserRegistered;
use Tailgate\Common\PasswordHashing\PasswordHashingInterface;
use Tailgate\Common\Security\RandomStringInterface;
use Tailgate\Infrastructure\Persistence\Repository\UserRepository;

class RegisterUserHandlerTest extends TestCase
{
    private $username = 'username';
    private $password = 'password';
    private $confirmPassword = 'password';
    private $email = 'email@email.com';
    private $uniqueKey = 'randomKey';
    private $registerUserCommand;

    public function setUp()
    {
        $this->registerUserCommand = new RegisterUserCommand(
            $this->username,
            $this->password,
            $this->email,
            $this->confirmPassword
        );
    }

    public function testItAddsAUserRegisteredToTheUserRepository()
    {
        $username = $this->username;
        $password = $this->password;
        $confirmPassword = $this->confirmPassword;
        $email = $this->email;
        $uniqueKey = $this->uniqueKey;

        // only needs the add method
        $userRepository = $this->getMockBuilder(UserRepository::class)
            ->disableOriginalConstructor()
            ->setMethods(['add'])
            ->getMock();

        // the add method should be called once
        // the user object should have the UserRegistered event
        $userRepository
            ->expects($this->once())
            ->method('add')
            ->with($this->callback(
                function ($user) use (
                $username,
                $password,
                $confirmPassword,
                $email,
                $uniqueKey
            ) {
                    $events = $user->getRecordedEvents();

                    return $events[0] instanceof UserRegistered
                && $events[0]->getAggregateId() instanceof UserId
                && $events[0]->getUsername() === $username
                && $events[0]->getPasswordHash() === $password
                && $events[0]->getEmail() === $email
                && $events[0]->getStatus() === User::STATUS_PENDING
                && $events[0]->getRole() === User::ROLE_USER
                && $events[0]->getUniqueKey() === $uniqueKey
                && $events[0]->getOccurredOn() instanceof \DateTimeImmutable;
                }
        ));

        $passwordHashing = $this->createMock(PasswordHashingInterface::class);
        $passwordHashing
            ->expects($this->once())
            ->method('hash')
            ->willReturn($password);

        $randomStringer = $this->createMock(RandomStringInterface::class);
        $randomStringer
            ->expects($this->once())
            ->method('generate')
            ->willReturn($uniqueKey);

        $registerUserHandler = new RegisterUserHandler(
            $userRepository,
            $passwordHashing,
            $randomStringer
        );

        $user = $registerUserHandler->handle($this->registerUserCommand);

        $this->assertNotEmpty($user['userId']);
        $this->assertEquals($this->username, $user['username']);
        $this->assertEquals($this->email, $user['email']);
        $this->assertEquals(User::STATUS_PENDING, $user['status']);
        $this->assertEquals(User::ROLE_USER, $user['role']);
    }
}
