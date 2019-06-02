<?php

namespace Tailgate\Application\Command\User;

use Tailgate\Domain\Model\User\User;
use Tailgate\Domain\Model\User\UserRepositoryInterface;
use Tailgate\Domain\Service\PasswordHashing\PasswordHashingInterface;

class SignUpUserHandler
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

    public function handle(SignUpUserCommand $signUpUserCommand)
    {
        $username = $signUpUserCommand->getUsername();
        $password = $signUpUserCommand->getPassword();
        $email = $signUpUserCommand->getEmail();

        // check for unique username and email
        // emailInterface to send confirmation email - place it here or...something asynchronous

        $user = User::create(
            $this->userRepository->nextIdentity(),
            $username,
            $this->passwordHashing->hash($password),
            $email
        );

        $this->userRepository->add($user);
    }
}