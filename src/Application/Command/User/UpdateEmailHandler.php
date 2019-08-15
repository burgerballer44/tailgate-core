<?php

namespace Tailgate\Application\Command\User;

use Tailgate\Domain\Model\User\User;
use Tailgate\Domain\Model\User\UserId;
use Tailgate\Domain\Model\User\UserRepositoryInterface;

class UpdateEmailHandler
{
    private $userRepository;

    public function __construct(
        UserRepositoryInterface $userRepository
    ) {
        $this->userRepository = $userRepository;
    }

    public function handle(UpdateEmailCommand $updateEmailCommand)
    {
        $userId = $updateEmailCommand->getUserId();
        $email = $updateEmailCommand->getEmail();

        $user = $this->userRepository->get(UserId::fromString($userId));

        $user->updateEmail($email);

        $this->userRepository->add($user);
    }
}
