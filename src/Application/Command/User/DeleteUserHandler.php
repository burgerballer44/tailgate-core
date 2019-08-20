<?php

namespace Tailgate\Application\Command\User;

use Tailgate\Domain\Model\User\User;
use Tailgate\Domain\Model\User\UserId;
use Tailgate\Domain\Model\User\UserRepositoryInterface;

class DeleteUserHandler
{
    private $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function handle(DeleteUserCommand $command)
    {
        $userId = $command->getUserId();

        $user = $this->userRepository->get(UserId::fromString($userId));

        $user->delete();

        $this->userRepository->add($user);
    }
}
