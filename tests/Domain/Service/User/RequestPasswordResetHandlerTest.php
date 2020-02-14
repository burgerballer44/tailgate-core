<?php

namespace Tailgate\Test\Domain\Service\User;

use PHPUnit\Framework\TestCase;
use Tailgate\Application\Command\User\RequestPasswordResetCommand;
use Tailgate\Application\Validator\ValidatorInterface;
use Tailgate\Domain\Service\Security\RandomStringInterface;
use Tailgate\Domain\Model\User\PasswordResetTokenCreated;
use Tailgate\Domain\Model\User\User;
use Tailgate\Domain\Model\User\UserId;
use Tailgate\Domain\Model\User\UserRepositoryInterface;
use Tailgate\Domain\Service\User\RequestPasswordResetHandler;

class RequestPasswordResetHandlerTest extends TestCase
{
    private $userId = 'userId';
    private $passwordHash = 'password';
    private $email = 'email@email.com';
    private $passwordResetToken = 'randomString';
    private $user;
    private $requestPasswordReset;

    public function setUp()
    {
        // create a user and clear events
        $this->user = User::create(
            UserId::fromString($this->userId),
            $this->email,
            $this->passwordHash
        );
        $this->user->clearRecordedEvents();

        $this->requestPasswordReset = new RequestPasswordResetCommand($this->userId);
    }

    public function testItAddsAPasswordResetTokenCreatedToTheRepository()
    {
        $userId = $this->userId;
        $user = $this->user;
        $passwordResetToken = $this->passwordResetToken;

        $userRepository = $this->getMockBuilder(UserRepositoryInterface::class)->getMock();

        // the get method should be called once and will return the user
        $userRepository->expects($this->once())->method('get')->willReturn($user);

        // the add method should be called once
        // the user object should have the PasswordResetTokenCreated event
        $userRepository->expects($this->once())->method('add')->with($this->callback(
            function ($user) use ($userId, $passwordResetToken) {
                $events = $user->getRecordedEvents();

                return $events[0] instanceof PasswordResetTokenCreated
                && $events[0]->getAggregateId()->equals(UserId::fromString($userId))
                && $passwordResetToken === substr($events[0]->getPasswordResetToken(), 0, strlen($passwordResetToken))
                && '_' === substr($events[0]->getPasswordResetToken(), strlen($passwordResetToken), 1)
                && 10 === strlen(substr($events[0]->getPasswordResetToken(), strlen($passwordResetToken) + 1))
                && $events[0]->getOccurredOn() instanceof \DateTimeImmutable;
            }
        ));

        $validator = $this->createMock(ValidatorInterface::class);
        $validator->expects($this->once())->method('assert')->willReturn(true);

        $randomStringer = $this->createMock(RandomStringInterface::class);
        $randomStringer->expects($this->once())->method('generate')->willReturn($passwordResetToken);

        $requestPasswordResethanlder = new RequestPasswordResetHandler($validator, $userRepository, $randomStringer);

        $requestPasswordResethanlder->handle($this->requestPasswordReset);
    }
}
