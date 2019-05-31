<?php

namespace Tailgate\Application\Command\User;

use Tailgate\Domain\Model\User\User;
use Tailgate\Domain\Model\User\UserRepositoryInterface;

class SignUpUserHandler
{
    private $userRepository;

    public function __construct(UserRepositoryInterface $userRepository) {
        $this->userRepository = $userRepository;
    }

    public function handle(SignUpUserCommand $signUpUserCommand)
    {
        $username = $signUpUserCommand->getUsername();
        $password = $signUpUserCommand->getPassword();
        $email = $signUpUserCommand->getEmail();

        // reminder: password_repeat should be checked before it touches the command
        // Todo: check unique username, email

        $user = User::create(
            $this->userRepository->nextIdentity(),
            $username,
            $password,
            $email
        );

        $this->userRepository->add($user);
    }
}