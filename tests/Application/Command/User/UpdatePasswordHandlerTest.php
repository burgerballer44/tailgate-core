<?php

namespace Tailgate\Test\Application\Command\User;

use PHPUnit\Framework\TestCase;
use Tailgate\Application\Command\User\UpdatePasswordCommand;
use Tailgate\Application\Command\User\UpdatePasswordHandler;
use Tailgate\Common\PasswordHashing\PasswordHashingInterface;
use Tailgate\Domain\Model\User\User;
use Tailgate\Domain\Model\User\UserId;
use Tailgate\Domain\Model\User\PasswordUpdated;
use Tailgate\Infrastructure\Persistence\Repository\UserRepository;

class UpdatePasswordHandlerTest extends TestCase
{
    private $userId = 'userId';
    private $username = 'username';
    private $passwordHash = 'password';
    private $email = 'email@email.com';
    private $key = 'randomKey';
    private $user;
    private $updatePasswordCommand;
    private $newPasswordHash = 'newPassword';

    public function setUp()
    {
        $this->user = User::create(
            UserId::fromString($this->userId),
            $this->username,
            $this->passwordHash,
            $this->email,
            $this->key
        );
        $this->user->clearRecordedEvents();

        $this->updatePasswordCommand = new UpdatePasswordCommand($this->userId, $this->newPasswordHash);
    }

    public function testItAddsAUserActivatedRepository()
    {
        $userId = $this->userId;
        $newPasswordHash = $this->newPasswordHash;
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
            ->with($this->callback(
                function ($user) use (
                $userId,
                $newPasswordHash
            ) {
                    $events = $user->getRecordedEvents();

                    return $events[0] instanceof PasswordUpdated
                && $events[0]->getAggregateId()->equals(UserId::fromString($userId))
                && $events[0]->getPasswordHash() === $newPasswordHash
                && $events[0]->getOccurredOn() instanceof \DateTimeImmutable;
                }
        ));

        $passwordHashing = $this->createMock(PasswordHashingInterface::class);
        $passwordHashing
            ->expects($this->once())
            ->method('hash')
            ->willReturn($newPasswordHash);

        $updatePasswordHandler = new UpdatePasswordHandler(
            $userRepository,
            $passwordHashing
        );

        $updatePasswordHandler->handle($this->updatePasswordCommand);
    }
}
