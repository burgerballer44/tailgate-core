<?php

namespace Tailgate\Domain\Service\User;

use Tailgate\Application\Command\User\ActivateUserCommand;
use Tailgate\Domain\Model\Common\Date;
use Tailgate\Domain\Model\User\UserId;
use Tailgate\Domain\Model\User\UserRepositoryInterface;
use Tailgate\Domain\Service\Clock\Clock;

class ActivateUserHandler
{
    private $clock;
    private $userRepository;

    public function __construct(Clock $clock, UserRepositoryInterface $userRepository)
    {
        $this->clock = $clock;
        $this->userRepository = $userRepository;
    }

    public function handle(ActivateUserCommand $command)
    {
        $user = $this->userRepository->get(UserId::fromString($command->getUserId()));

        $user->activate(Date::fromDateTimeImmutable($this->clock->currentTime()));

        $this->userRepository->add($user);
    }
}
