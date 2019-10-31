<?php

namespace Tailgate\Application\Command\User;

use Tailgate\Domain\Model\User\User;
use Tailgate\Domain\Model\User\UserId;
use Tailgate\Domain\Model\User\UserRepositoryInterface;
use Tailgate\Common\Security\RandomStringInterface;

class RequestPasswordResetHandler
{
    private $userRepository;
    private $randomStringer;

    public function __construct(
        UserRepositoryInterface $userRepository,
        RandomStringInterface $randomStringer
    ) {
        $this->userRepository = $userRepository;
        $this->randomStringer = $randomStringer;
    }

    public function handle(RequestPasswordResetCommand $command)
    {
        $userId = $command->getUserId();

        $user = $this->userRepository->get(UserId::fromString($userId));

        $user->createPasswordResetToken($this->randomStringer->generate());

        $this->userRepository->add($user);

        return [
            'userId'             => $user->getId(),
            'passwordResetToken' => $user->getPasswordResetToken()
        ];
    }
}
