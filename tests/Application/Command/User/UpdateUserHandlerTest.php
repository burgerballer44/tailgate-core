<?php

namespace Tailgate\Test\Application\Command\User;

use PHPUnit\Framework\TestCase;
use Tailgate\Application\Command\User\UpdateUserCommand;
use Tailgate\Application\Command\User\UpdateUserHandler;
use Tailgate\Domain\Model\User\User;
use Tailgate\Domain\Model\User\UserId;
use Tailgate\Domain\Model\User\UserUpdated;
use Tailgate\Domain\Model\User\UserRepositoryInterface;

class UpdateUserHandlerTest extends TestCase
{
    private $userId = 'userId';
    private $username = 'usernameUpdated';
    private $email = 'updated@email.com';
    private $status = 'statusUpdated';
    private $role = 'roleUpdated';
    private $user;
    private $updateUserCommand;

    public function setUp()
    {
        $this->user = User::create(
            UserId::fromString($this->userId),
            'username',
            'password',
            'email@email.com',
            'randomKey'
        );
        $this->user->clearRecordedEvents();

        $this->updateUserCommand = new UpdateUserCommand(
            $this->userId,
            $this->username,
            $this->email,
            $this->status,
            $this->role
        );
    }

    public function testItAddsAUserUpdatedToTheUserRepository()
    {
        $userId = $this->userId;
        $username = $this->username;
        $email = $this->email;
        $status = $this->status;
        $role = $this->role;

        $userRepository = $this->getMockBuilder(UserRepositoryInterface::class)->getMock();

        // the get method should be called once and will return the user
        $userRepository->expects($this->once())->method('get')->willReturn($this->user);

        // the add method should be called once
        // the user object should have the UserUpdated event
        $userRepository->expects($this->once())->method('add')->with($this->callback(
            function ($user) use ($userId, $username, $email, $status, $role) {
                $events = $user->getRecordedEvents();

                return $events[0] instanceof UserUpdated
                && $events[0]->getAggregateId()->equals(UserId::fromString($userId))
                && $events[0]->getUsername() === $username
                && $events[0]->getEmail() === $email
                && $events[0]->getStatus() === $status
                && $events[0]->getRole() === $role
                && $events[0]->getOccurredOn() instanceof \DateTimeImmutable;
            }
        ));

        $updateUserHandler = new UpdateUserHandler($userRepository);

        $updateUserHandler->handle($this->updateUserCommand);
    }
}
