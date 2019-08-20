<?php

namespace Tailgate\Application\Command\User;

use Tailgate\Domain\Model\User\UserId;
use Tailgate\Domain\Model\User\UserRepositoryInterface;

class UpdateUserHandler
{
    private $userRepository;
    private $passwordHashing;
    private $randomStringer;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function handle(UpdateUserCommand $command)
    {
        $userId = $command->getUserId();
        $username = $command->getUsername();
        $email = $command->getEmail();
        $status = $command->getStatus();
        $role = $command->getRole();

        $user = $this->userRepository->get(UserId::fromString($userId));

        $user->update($username, $email, $status, $role);

        $this->userRepository->add($user);
    }
}
