<?php

namespace Tailgate\Application\Command\User;

use Tailgate\Domain\Model\User\User;
use Tailgate\Domain\Model\User\UserRepositoryInterface;
use Tailgate\Common\PasswordHashing\PasswordHashingInterface;
use Tailgate\Common\Security\RandomStringInterface;

class SignUpUserHandler
{
    private $userRepository;
    private $passwordHashing;
    private $randomStringer;

    public function __construct(
        UserRepositoryInterface $userRepository,
        PasswordHashingInterface $passwordHashing,
        RandomStringInterface $randomStringer
    ) {
        $this->userRepository = $userRepository;
        $this->passwordHashing = $passwordHashing;
        $this->randomStringer = $randomStringer;
    }

    public function handle(SignUpUserCommand $signUpUserCommand)
    {
        $username = $signUpUserCommand->getUsername();
        $password = $signUpUserCommand->getPassword();
        $email = $signUpUserCommand->getEmail();

        $user = User::create(
            $this->userRepository->nextIdentity(),
            $username,
            $this->passwordHashing->hash($password),
            $email,
            $this->randomStringer->generate()
        );

        $this->userRepository->add($user);
    }
}