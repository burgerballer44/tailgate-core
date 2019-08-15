<?php

namespace Tailgate\Application\Command\User;

use Tailgate\Domain\Model\User\User;
use Tailgate\Domain\Model\User\UserRepositoryInterface;
use Tailgate\Common\PasswordHashing\PasswordHashingInterface;
use Tailgate\Common\Security\RandomStringInterface;

class RegisterUserHandler
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

    public function handle(RegisterUserCommand $registerUserCommand)
    {
        $username = $registerUserCommand->getUsername();
        $password = $registerUserCommand->getPassword();
        $email = $registerUserCommand->getEmail();

        $user = User::create(
            $this->userRepository->nextIdentity(),
            $username,
            $this->passwordHashing->hash($password),
            $email,
            $this->randomStringer->generate()
        );

        $this->userRepository->add($user);

        return [
            'userId'   => $user->getId(),
            'username' => $user->getUsername(),
            'email'    => $user->getEmail(),
            'status'   => $user->getStatus(),
            'role'     => $user->getRole()
        ];
    }
}
