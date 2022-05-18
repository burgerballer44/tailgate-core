<?php

namespace Tailgate\Domain\Service\User;

use Tailgate\Application\Command\User\RegisterUserCommand;
use Tailgate\Application\Validator\ValidatorInterface;
use Tailgate\Domain\Model\Common\Date;
use Tailgate\Domain\Model\User\User;
use Tailgate\Domain\Model\User\UserRepositoryInterface;
use Tailgate\Domain\Service\Clock\Clock;
use Tailgate\Domain\Service\PasswordHashing\PasswordHashingInterface;
use Tailgate\Domain\Service\Validatable;
use Tailgate\Domain\Service\ValidatableService;

class RegisterUserHandler implements ValidatableService
{
    use Validatable;

    private $validator;
    private $clock;
    private $userRepository;
    private $passwordHashing;

    public function __construct(
        ValidatorInterface $validator,
        Clock $clock,
        UserRepositoryInterface $userRepository,
        PasswordHashingInterface $passwordHashing
    ) {
        $this->validator = $validator;
        $this->clock = $clock;
        $this->userRepository = $userRepository;
        $this->passwordHashing = $passwordHashing;
    }

    public function handle(RegisterUserCommand $command)
    {
        $this->validate($command);

        $user = User::register(
            $this->userRepository->nextIdentity(),
            $command->getEmail(),
            $this->passwordHashing->hash($command->getPassword()),
            Date::fromDateTimeImmutable($this->clock->currentTime())
        );

        $this->userRepository->add($user);

        return [
            'userId' => $user->getUserId(),
            'email' => $user->getEmail(),
            'status' => $user->getStatus(),
            'role' => $user->getRole(),
        ];
    }
}
