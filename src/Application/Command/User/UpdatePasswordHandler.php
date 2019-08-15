<?php

namespace Tailgate\Application\Command\User;

use Tailgate\Common\PasswordHashing\PasswordHashingInterface;
use Tailgate\Domain\Model\User\User;
use Tailgate\Domain\Model\User\UserId;
use Tailgate\Domain\Model\User\UserRepositoryInterface;

class UpdatePasswordHandler
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

    public function handle(UpdatePasswordCommand $updatePasswordCommand)
    {
        $userId = $updatePasswordCommand->getUserId();
        $password = $updatePasswordCommand->getPassword();

        $user = $this->userRepository->get(UserId::fromString($userId));

        $user->updatePassword($this->passwordHashing->hash($password));

        $this->userRepository->add($user);
    }
}
