<?php

namespace Tailgate\Domain\Service\User;

use Tailgate\Application\Command\User\RegisterUserCommand;
use Tailgate\Domain\Model\Common\Date;
use Tailgate\Domain\Model\Common\Email;
use Tailgate\Domain\Model\User\User;
use Tailgate\Domain\Model\User\UserRepositoryInterface;
use Tailgate\Domain\Service\Clock\Clock;
use Tailgate\Domain\Service\PasswordHashing\PasswordHashingInterface;

class RegisterUserHandler
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

    public function handle(RegisterUserCommand $command)
    {
        $user = User::register(
            $this->userRepository->nextIdentity(),
            Email::fromString($command->getEmail()),
            $this->passwordHashing->hash($command->getPassword()),
            Date::fromDateTimeImmutable($this->clock->currentTime())
        );

        $this->userRepository->add($user);

        // we want to return our newly created user
        return [
            'user_id' => (string) $user->getUserId(),
            'email' => (string) $user->getEmail(),
            'status' => (string) $user->getStatus(),
            'role' => (string) $user->getRole(),
        ];
    }
}
