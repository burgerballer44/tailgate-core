<?php

namespace Tailgate\Test\Application\Command\User;

use PHPUnit\Framework\TestCase;
use Tailgate\Application\Command\User\SignUpUserCommand;
use Tailgate\Application\Command\User\SignUpUserHandler;
use Tailgate\Domain\Model\User\User;
use Tailgate\Infrastructure\Persistence\Repository\UserRepository;

class SignUpUserHandlerTest extends TestCase
{
    private $userRepository;
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
            )  {
                return $user instanceof User
                && $user->getUsername() === $username
                && $user->getPassword() === $password
                && $user->getEmail() === $email;
            }
        ));

        $this->signUpUserCommandHandler = new SignUpUserHandler(
            $this->userRepository
        );
    }

    public function testItAttemptsToAddAUserToTheUserRepository()
    {
        $this->signUpUserCommandHandler->handle($this->signUpUserCommand);
    }
}
