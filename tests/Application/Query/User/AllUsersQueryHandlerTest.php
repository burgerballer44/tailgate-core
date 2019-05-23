<?php

namespace Tailgate\Test\Application\Command\User;

use PHPUnit\Framework\TestCase;
use Tailgate\Application\Command\User\SignUpUserCommand;
use Tailgate\Application\Command\User\SignUpUserHandler;
use Tailgate\Application\Query\User\AllUsersQuery;
use Tailgate\Application\Query\User\AllUsersQueryHandler;
use Tailgate\Domain\Model\User\User;
use Tailgate\Infrastructure\Persistence\EventStore\InMemory\InMemoryEventStore;
use Tailgate\Infrastructure\Persistence\Projection\InMemory\InMemoryUserProjectionViewRepository;
use Tailgate\Infrastructure\Persistence\Repository\UserRepository;

class AllUsersQueryHandlerTest extends TestCase
{
    private $username = 'username';
    private $password = 'password';
    private $email = 'email@email.com';
    private $userRepository;
    private $userProjectionViewRepository;
    private $signUpUserCommand;
    private $signUpUserCommandHandler;

    public function setUp()
    {
        $this->signUpUserCommand = new SignUpUserCommand(
            $this->username, 
            $this->password, 
            $this->email
        );

        $this->userProjectionViewRepository = new InMemoryUserProjectionViewRepository();

        $this->userRepository = new UserRepository(
            new InMemoryEventStore,
            $this->userProjectionViewRepository
        );

        $this->signUpUserCommandHandler = new SignUpUserHandler(
            $this->userRepository
        );
    }

    private function signUpAUser()
    {
        $this->signUpUserCommandHandler->handle($this->signUpUserCommand);
    }

    public function testAllUsersCanBeRetrieved()
    {
        $allUsersQuery = new AllUsersQuery();
        $allUsersQueryHandler = new AllUsersQueryHandler($this->userProjectionViewRepository);

        $users = $allUsersQueryHandler->handle($allUsersQuery);
        $this->assertCount(0, $users);

        $this->signUpAUser();
        $this->signUpAUser();
        $users = $allUsersQueryHandler->handle($allUsersQuery);
        $this->assertCount(2, $users);
    }
}
