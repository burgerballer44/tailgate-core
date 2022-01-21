<?php

namespace Tailgate\Domain\Service\User;

use Tailgate\Application\Command\User\UpdateUserCommand;
use Tailgate\Application\Validator\ValidatorInterface;
use Tailgate\Domain\Model\Common\Date;
use Tailgate\Domain\Model\User\UserId;
use Tailgate\Domain\Model\User\UserRepositoryInterface;
use Tailgate\Domain\Service\Clock\Clock;
use Tailgate\Domain\Service\Validatable;
use Tailgate\Domain\Service\ValidatableService;

class UpdateUserHandler implements ValidatableService
{
    use Validatable;
    
    private $validator;
    private $clock;
    private $userRepository;

    public function __construct(ValidatorInterface $validator, Clock $clock, UserRepositoryInterface $userRepository)
    {
        $this->validator = $validator;
        $this->clock = $clock;
        $this->userRepository = $userRepository;
    }

    public function handle(UpdateUserCommand $command)
    {
        $this->validate($command);

        $user = $this->userRepository->get(UserId::fromString($command->getUserId()));

        $user->update($command->getEmail(), $command->getStatus(), $command->getRole(), Date::fromDateTimeImmutable($this->clock->currentTime()));

        $this->userRepository->add($user);
    }
}
