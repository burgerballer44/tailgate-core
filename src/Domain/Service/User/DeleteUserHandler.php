<?php

namespace Tailgate\Domain\Service\User;

use Tailgate\Application\Command\User\DeleteUserCommand;
use Tailgate\Domain\Model\Common\Date;
use Tailgate\Domain\Model\User\UserId;
use Tailgate\Domain\Model\User\UserRepositoryInterface;
use Tailgate\Domain\Service\Clock\Clock;

class DeleteUserHandler
{
    private $userRepository;
    private $clock;

    public function __construct(UserRepositoryInterface $userRepository, Clock $clock)
    {
        $this->userRepository = $userRepository;
        $this->clock = $clock;
    }

    public function handle(DeleteUserCommand $command)
    {
        $userId = $command->getUserId();

        $user = $this->userRepository->get(UserId::fromString($userId));

        $user->delete(Date::fromDateTimeImmutable($this->clock->currentTime()));

        $this->userRepository->add($user);
    }
}
