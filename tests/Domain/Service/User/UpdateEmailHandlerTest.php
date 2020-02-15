<?php

namespace Tailgate\Test\Domain\Service\User;

use PHPUnit\Framework\TestCase;
use Tailgate\Application\Command\User\UpdateEmailCommand;
use Tailgate\Application\Validator\ValidatorInterface;
use Tailgate\Domain\Model\User\EmailUpdated;
use Tailgate\Domain\Model\User\User;
use Tailgate\Domain\Model\User\UserId;
use Tailgate\Domain\Model\User\UserRepositoryInterface;
use Tailgate\Domain\Service\User\UpdateEmailHandler;

class UpdateEmailHandlerTest extends TestCase
{
    private $userId = 'userId';
    private $passwordHash = 'password';
    private $email = 'email@email.com';
    private $user;
    private $updateEmailCommand;
    private $newEmail = 'newEmail@email.coim';

    public function setUp(): void
    {
        // create a team and clear events
        $this->user = User::create(
            UserId::fromString($this->userId),
            $this->email,
            $this->passwordHash
        );
        $this->user->clearRecordedEvents();

        $this->updateEmailCommand = new UpdateEmailCommand($this->userId, $this->newEmail);
    }

    public function testItAddsAEmailUpdatedRepository()
    {
        $userId = $this->userId;
        $newEmail = $this->newEmail;
        $user = $this->user;

        $userRepository = $this->getMockBuilder(UserRepositoryInterface::class)->getMock();

        // the get method should be called once and will return the user
        $userRepository->expects($this->once())->method('get')->willReturn($user);

        // the add method should be called once
        // the user object should have the UserActivated event
        $userRepository->expects($this->once())->method('add')->with($this->callback(
            function ($user) use ($userId, $newEmail) {
                $events = $user->getRecordedEvents();

                return $events[0] instanceof EmailUpdated
                && $events[0]->getAggregateId()->equals(UserId::fromString($userId))
                && $events[0]->getEmail() === $newEmail
                && $events[0]->getOccurredOn() instanceof \DateTimeImmutable;
            }
        ));

        $validator = $this->createMock(ValidatorInterface::class);
        $validator->expects($this->once())->method('assert')->willReturn(true);

        $updateEmailHandler = new updateEmailHandler($validator, $userRepository);

        $updateEmailHandler->handle($this->updateEmailCommand);
    }
}
