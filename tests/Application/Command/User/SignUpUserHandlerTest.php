<?php

namespace Tailgate\Test\Application\Command\User;

use PHPUnit\Framework\TestCase;
use Tailgate\Application\Command\User\SignUpUserCommand;
use Tailgate\Application\Command\User\SignUpUserHandler;
use Tailgate\Domain\Model\User\User;
use Tailgate\Domain\Service\PasswordHashing\PasswordHashingInterface;
use Tailgate\Infrastructure\Persistence\Repository\UserRepository;

class SignUpUserHandlerTest extends TestCase
{
    private $userRepository;
    private $passwordHashing;
    private $signUpUserCommand;
    private $signUpUserCommandHandler;

    public function setUp()
    {
        $username = 'username';
        $password = 'password';
        $email = 'email@email.com';

        $this->signUpUserCommand = new SignUpUserCommand(
            $username, 
            $password, 
            $email
        );

        $this->userRepository = $this->getMockBuilder(UserRepository::class)
            ->disableOriginalConstructor()
            ->setMethods(['add'])
            ->getMock();

        $this->userRepository
            ->expects($this->once())
            ->method('add')
            ->with($this->callback(function($user) use (
                $username,
                $password,
                $email
            ) {
                return $user instanceof User
                && $user->getUsername() === $username
                && $user->getPasswordHash() === $password
                && $user->getEmail() === $email;
            }
        ));

        $this->passwordHashing = $this->createMock(PasswordHashingInterface::class);
        $this->passwordHashing
            ->expects($this->once())
            ->method('hash')
            ->willReturn($password);

        $this->signUpUserCommandHandler = new SignUpUserHandler(
            $this->userRepository,
            $this->passwordHashing
        );
    }

    public function testItAttemptsToAddAUserToTheUserRepository()
    {
        $this->signUpUserCommandHandler->handle($this->signUpUserCommand);
    }
}
