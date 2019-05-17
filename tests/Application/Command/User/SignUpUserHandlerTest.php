<?php

namespace Tailgate\Test\Application\Command\User;

use PHPUnit\Framework\TestCase;
use Tailgate\Domain\Model\User\UserId;
use Tailgate\Application\Command\User\SignUpUser;
use Tailgate\Application\Command\User\SignUpUserHandler;
use Tailgate\Infrastructure\Persistence\Repository\InMemory\InMemoryUserRepository;
use Tailgate\Application\DataTransformer\User\UserDtoDataTransformer;

class SignUpUserHandlerTest extends TestCase
{
    private $username = 'username';
    private $password = 'password';
    private $email = 'email@email.com';
    private $userRepository;
    private $userDataTransformer;
    private $signUpUserCommand;
    private $signUpUserCommandHandler;

    public function setUp()
    {
        $this->signUpUserCommand = new SignUpUser(
            $this->username, 
            $this->password, 
            $this->email
        );
        $this->userRepository = new InMemoryUserRepository();
        $this->userDataTransformer = new UserDtoDataTransformer();
        $this->signUpUserCommandHandler = new SignUpUserHandler(
            $this->userRepository, $this->userDataTransformer
        );
    }

    public function testItSignsUpANewUserByAddingToTheUserRepository()
    {
        $user = $this->signUpUserCommandHandler->handle($this->signUpUserCommand);

        $userFromRepository = $this->userRepository->byId(new UserId($user['id']));

        $this->assertNotNull($userFromRepository);
        $this->assertEquals($this->username, $userFromRepository->getUsername());
        $this->assertEquals($this->password, $userFromRepository->getPassword());
        $this->assertEquals($this->email, $userFromRepository->getEmail());
    }
}
