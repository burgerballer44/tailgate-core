<?php

namespace Tailgate\Domain\Service\User;

use Tailgate\Application\Command\User\UpdateEmailCommand;
use Tailgate\Domain\Model\Common\Date;
use Tailgate\Domain\Model\Common\Email;
use Tailgate\Domain\Model\User\UserId;
use Tailgate\Domain\Model\User\UserRepositoryInterface;
use Tailgate\Domain\Service\Clock\Clock;

class UpdateEmailHandler
{
    private $clock;
    private $userRepository;

    public function __construct(Clock $clock, UserRepositoryInterface $userRepository)
    {
        $this->clock = $clock;
        $this->userRepository = $userRepository;
    }

    public function handle(UpdateEmailCommand $command)
    {
        $user = $this->userRepository->get(UserId::fromString($command->getUserId()));

        $user->updateEmail(
            Email::fromString($command->getEmail()),
            Date::fromDateTimeImmutable($this->clock->currentTime())
        );

        $this->userRepository->add($user);
    }
}
