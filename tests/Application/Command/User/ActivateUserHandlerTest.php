<?php

namespace Tailgate\Test\Application\Command\User;

use PHPUnit\Framework\TestCase;
use Tailgate\Application\Command\User\ActivateUserCommand;
use Tailgate\Application\Command\User\ActivateUserHandler;
use Tailgate\Domain\Model\User\User;
use Tailgate\Domain\Model\User\UserId;
use Tailgate\Domain\Model\User\UserActivated;
use Tailgate\Infrastructure\Persistence\Repository\UserRepository;

class ActivateUserHandlerTest extends TestCase
{
    private $userId = 'userId';
    private $username = 'username';
    private $passwordHash = 'password';
    private $email = 'email@email.com';
    private $key = 'randomKey';
    private $user;
    private $activateUserCommand;

    public function setUp()
    {
        $this->user = User::create(
            UserId::fromString($this->userId), $this->username, $this->passwordHash, $this->email, $this->key
        );
        $this->user->clearRecordedEvents();

        $this->activateUserCommand = new ActivateUserCommand($this->userId);
    }

    public function testItAddsAUserActivatedRepository()
    {
        $userId = $this->userId;
        $user = $this->user;

        // only needs the get and add method
        $userRepository = $this->getMockBuilder(UserRepository::class)
            ->disableOriginalConstructor()
            ->setMethods(['get', 'add'])
            ->getMock();

        // the get method should be called once and will return the user
        $userRepository
           ->expects($this->once())
           ->method('get')
           ->willReturn($user);

        // the add method should be called once
        // the user object should have the UserActivated event
        $userRepository
            ->expects($this->once())
            ->method('add')
            ->with($this->callback(function($user) use (
                $userId
            ) {
                $events = $user->getRecordedEvents();

                return $events[0] instanceof UserActivated
                && $events[0]->getAggregateId()->equals(UserId::fromString($userId))
                && $events[0]->getStatus() === User::STATUS_ACTIVE
                && $events[0]->getOccurredOn() instanceof \DateTimeImmutable;
            }
        ));

        $activateUserHandler = new ActivateUserHandler($userRepository);

        $activateUserHandler->handle($this->activateUserCommand);
    }
}
