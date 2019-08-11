<?php

namespace Tailgate\Application\Command\User;

use Tailgate\Domain\Model\User\User;
use Tailgate\Domain\Model\User\UserId;
use Tailgate\Domain\Model\User\UserRepositoryInterface;

class ActivateUserHandler
{
    private $userRepository;

    public function __construct(
        UserRepositoryInterface $userRepository
    ) {
        $this->userRepository = $userRepository;
    }

    public function handle(ActivateUserCommand $activateUserCommand)
    {
        $userId = $activateUserCommand->getUserId();

        $user = $this->userRepository->get(UserId::fromString($userId));

        $user->activate();

        $this->userRepository->add($user);
    }
}