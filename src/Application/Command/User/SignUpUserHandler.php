<?php

namespace Tailgate\Application\Command\User;

use Tailgate\Domain\Model\User\User;
use Tailgate\Domain\Model\User\UserRepositoryInterface;

/**
 * handles a person attempting to sign up
 */
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

        $user = User::create(
            $this->userRepository->nextIdentity(),
            $username,
            $password,
            $email
        );

        $this->userRepository->add($user);
    }
}