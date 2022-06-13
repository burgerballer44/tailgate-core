<?php

namespace Tailgate\Domain\Service\User;

use Tailgate\Application\Command\User\ResetPasswordCommand;
use Tailgate\Domain\Model\Common\Date;
use Tailgate\Domain\Model\User\UserId;
use Tailgate\Domain\Model\User\UserRepositoryInterface;
use Tailgate\Domain\Service\Clock\Clock;
use Tailgate\Domain\Service\PasswordHashing\PasswordHashingInterface;

class ResetPasswordHandler
{
    private $clock;
    private $userRepository;
    private $passwordHashing;

    public function __construct(
        Clock $clock,
        UserRepositoryInterface $userRepository,
        PasswordHashingInterface $passwordHashing
    ) {
        $this->clock = $clock;
        $this->userRepository = $userRepository;
        $this->passwordHashing = $passwordHashing;
    }

    public function handle(ResetPasswordCommand $command)
    {
        $user = $this->userRepository->get(UserId::fromString($command->getUserId()));

        $user->updatePassword(
            $this->passwordHashing->hash($command->getPassword()),
            Date::fromDateTimeImmutable($this->clock->currentTime())
        );

        $this->userRepository->add($user);
    }
}
