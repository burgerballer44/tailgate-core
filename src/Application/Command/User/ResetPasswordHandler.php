<?php

namespace Tailgate\Application\Command\User;

use Tailgate\Common\PasswordHashing\PasswordHashingInterface;
use Tailgate\Domain\Model\User\User;
use Tailgate\Domain\Model\User\UserId;
use Tailgate\Domain\Model\User\UserRepositoryInterface;

class ResetPasswordHandler
{
    private $userRepository;
    private $passwordHashing;

    public function __construct(
        UserRepositoryInterface $userRepository,
        PasswordHashingInterface $passwordHashing
    ) {
        $this->userRepository = $userRepository;
        $this->passwordHashing = $passwordHashing;
    }

    public function handle(ResetPasswordCommand $command)
    {
        $userId = $command->getUserId();
        $password = $command->getPassword();

        $user = $this->userRepository->get(UserId::fromString($userId));

        $user->updatePassword($this->passwordHashing->hash($password));

        $this->userRepository->add($user);
    }
}
