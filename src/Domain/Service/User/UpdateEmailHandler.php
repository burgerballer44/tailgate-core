<?php

namespace Tailgate\Domain\Service\User;

use Tailgate\Application\Command\User\UpdateEmailCommand;
use Tailgate\Application\Validator\ValidatorInterface;
use Tailgate\Domain\Model\User\User;
use Tailgate\Domain\Model\User\UserId;
use Tailgate\Domain\Model\User\UserRepositoryInterface;
use Tailgate\Domain\Service\Validatable;
use Tailgate\Domain\Service\ValidatableService;

class UpdateEmailHandler implements ValidatableService
{
    use Validatable;
    
    private $userRepository;

    public function __construct(ValidatorInterface $validator, UserRepositoryInterface $userRepository)
    {
        $this->validator = $validator;
        $this->userRepository = $userRepository;
    }

    public function handle(UpdateEmailCommand $command)
    {
        $this->validate($command);
        
        $userId = $command->getUserId();
        $email = $command->getEmail();

        $user = $this->userRepository->get(UserId::fromString($userId));

        $user->updateEmail($email);

        $this->userRepository->add($user);
    }
}
