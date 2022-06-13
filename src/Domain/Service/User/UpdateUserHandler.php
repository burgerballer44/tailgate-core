<?php

namespace Tailgate\Domain\Service\User;

use Tailgate\Application\Command\User\UpdateUserCommand;
use Tailgate\Domain\Model\Common\Date;
use Tailgate\Domain\Model\Common\Email;
use Tailgate\Domain\Model\User\UserId;
use Tailgate\Domain\Model\User\UserRepositoryInterface;
use Tailgate\Domain\Model\User\UserRole;
use Tailgate\Domain\Model\User\UserStatus;
use Tailgate\Domain\Service\Clock\Clock;

class UpdateUserHandler
{
    private $clock;
    private $userRepository;

    public function __construct(Clock $clock, UserRepositoryInterface $userRepository)
    {
        $this->clock = $clock;
        $this->userRepository = $userRepository;
    }

    public function handle(UpdateUserCommand $command)
    {
        $user = $this->userRepository->get(UserId::fromString($command->getUserId()));

        $user->update(
            Email::fromString($command->getEmail()),
            UserStatus::fromString($command->getStatus()),
            UserRole::fromString($command->getRole()),
            Date::fromDateTimeImmutable($this->clock->currentTime())
        );

        $this->userRepository->add($user);
    }
}
