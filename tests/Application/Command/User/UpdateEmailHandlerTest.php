<?php

namespace Tailgate\Test\Application\Command\User;

use PHPUnit\Framework\TestCase;
use Tailgate\Application\Command\User\UpdateEmailCommand;
use Tailgate\Application\Command\User\UpdateEmailHandler;
use Tailgate\Domain\Model\User\User;
use Tailgate\Domain\Model\User\UserId;
use Tailgate\Domain\Model\User\EmailUpdated;
use Tailgate\Infrastructure\Persistence\Repository\UserRepository;

class UpdateEmailHandlerTest extends TestCase
{
    private $userId = 'userId';
    private $username = 'username';
    private $passwordHash = 'password';
    private $email = 'email@email.com';
    private $key = 'randomKey';
    private $user;
    private $updateEmailCommand;
    private $newEmail = 'newEmail@email.coim';

    public function setUp()
    {
        $this->user = User::create(
            UserId::fromString($this->userId), $this->username, $this->passwordHash, $this->email, $this->key
        );
        $this->user->clearRecordedEvents();

        $this->updateEmailCommand = new UpdateEmailCommand($this->userId, $this->newEmail);
    }

    public function testItAddsAUserActivatedRepository()
    {
        $userId = $this->userId;
        $newEmail = $this->newEmail;
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
                $userId,
                $newEmail
            ) {
                $events = $user->getRecordedEvents();

                return $events[0] instanceof EmailUpdated
                && $events[0]->getAggregateId()->equals(UserId::fromString($userId))
                && $events[0]->getEmail() === $newEmail
                && $events[0]->getOccurredOn() instanceof \DateTimeImmutable;
            }
        ));

        $updateEmailHandler = new updateEmailHandler($userRepository);

        $updateEmailHandler->handle($this->updateEmailCommand);
    }
}
