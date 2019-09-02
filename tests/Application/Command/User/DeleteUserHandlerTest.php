<?php

namespace Tailgate\Test\Application\Command\User;

use PHPUnit\Framework\TestCase;
use Tailgate\Application\Command\User\DeleteUserCommand;
use Tailgate\Application\Command\User\DeleteUserHandler;
use Tailgate\Domain\Model\User\User;
use Tailgate\Domain\Model\User\UserId;
use Tailgate\Domain\Model\User\UserDeleted;
use Tailgate\Domain\Model\User\UserRepositoryInterface;

class DeleteUserHandlerTest extends TestCase
{
    private $userId = 'userId';
    private $passwordHash = 'password';
    private $email = 'email@email.com';
    private $key = 'randomKey';
    private $user;
    private $deleteUserCommand;

    public function setUp()
    {
        // create a team and clear events
        $this->user = User::create(
            UserId::fromString($this->userId),
            $this->email,
            $this->passwordHash,
            $this->key
        );
        $this->user->clearRecordedEvents();

        $this->deleteUserCommand = new DeleteUserCommand($this->userId);
    }

    public function testItAddsAUserDeletedToTheRepository()
    {
        $userId = $this->userId;
        $user = $this->user;

        $userRepository = $this->getMockBuilder(UserRepositoryInterface::class)->getMock();

        // the get method should be called once and will return the user
        $userRepository->expects($this->once())->method('get')->willReturn($user);

        // the add method should be called once
        // the user object should have the UserDeleted event
        $userRepository->expects($this->once())->method('add')->with($this->callback(
            function ($user) use ($userId) {
                $events = $user->getRecordedEvents();

                return $events[0] instanceof UserDeleted
                && $events[0]->getAggregateId()->equals(UserId::fromString($userId))
                && $events[0]->getStatus() === User::STATUS_DELETED
                && $events[0]->getOccurredOn() instanceof \DateTimeImmutable;
            }
        ));

        $deleteUserHandler = new DeleteUserHandler($userRepository);

        $deleteUserHandler->handle($this->deleteUserCommand);
    }
}
