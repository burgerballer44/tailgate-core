<?php

namespace Tailgate\Test\Application\Command\User;

use PHPUnit\Framework\TestCase;
use Tailgate\Application\Command\User\SignUpUserCommand;
use Tailgate\Application\Command\User\SignUpUserHandler;
use Tailgate\Domain\Model\User\User;
use Tailgate\Infrastructure\Persistence\Repository\UserRepository;

class SignUpUserHandlerTest extends TestCase
{
    private $username = 'username';
    private $password = 'password';
    private $email = 'email@email.com';
    private $userRepository;
    private $signUpUserCommand;
    private $signUpUserCommandHandler;

    public function setUp()
    {
        $this->signUpUserCommand = new SignUpUserCommand(
            $this->username, 
            $this->password, 
            $this->email
        );

        $this->userRepository = $this->getMockBuilder(UserRepository::class)
            ->disableOriginalConstructor()
            ->setMethods(['add'])
            ->getMock();

         $this->userRepository
            ->expects($this->once())
            ->method('add')
            ->with($this->callback(function($user) {
                return $user instanceof User;
            }));

        $this->signUpUserCommandHandler = new SignUpUserHandler(
            $this->userRepository
        );
    }

    public function testItAttemptsToAddAUserToTheUserRepository()
    {
        $this->signUpUserCommandHandler->handle($this->signUpUserCommand);
    }
}
