<?php

namespace Tailgate\Domain\Service\User;

use Tailgate\Application\Command\User\ActivateUserCommand;
use Tailgate\Application\Validator\ValidatorInterface;
use Tailgate\Domain\Model\User\User;
use Tailgate\Domain\Model\User\UserId;
use Tailgate\Domain\Model\User\UserRepositoryInterface;
use Tailgate\Domain\Service\Validatable;
use Tailgate\Domain\Service\ValidatableService;

class ActivateUserHandler implements ValidatableService
{
    use Validatable;
    
    private $validator;
    private $userRepository;

    public function __construct(ValidatorInterface $validator, UserRepositoryInterface $userRepository)
    {
        $this->validator = $validator;
        $this->userRepository = $userRepository;
    }

    public function handle(ActivateUserCommand $command)
    {
        $this->validate($command);
        
        $userId = $command->getUserId();

        $user = $this->userRepository->get(UserId::fromString($userId));

        $user->activate();

        $this->userRepository->add($user);
    }
}
