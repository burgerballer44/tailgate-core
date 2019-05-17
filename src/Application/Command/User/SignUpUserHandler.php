<?php

namespace Tailgate\Application\Command\User;

use Tailgate\Domain\Model\User\User;
use Tailgate\Domain\Model\User\UserRepositoryInterface;
use Tailgate\Application\DataTransformer\User\UserDataTransformer;

/**
 * handles a person attempting to sign up
 */
class SignUpUserHandler
{
    private $userRepository;
    private $userDataTransformer;

    public function __construct(
        UserRepositoryInterface $userRepository,
        UserDataTransformer $userDataTransformer
    ) {
        $this->userRepository = $userRepository;
        $this->userDataTransformer = $userDataTransformer;
    }

    public function handle(SignUpUser $signUpUserCommand)
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
        $this->userDataTransformer->write($user);

        return $this->userDataTransformer->read();
    }
}