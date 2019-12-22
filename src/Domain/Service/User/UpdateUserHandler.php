<?php

namespace Tailgate\Domain\Service\User;

use Tailgate\Application\Command\User\UpdateUserCommand;
use Tailgate\Application\Validator\ValidatorInterface;
use Tailgate\Domain\Model\User\UserId;
use Tailgate\Domain\Model\User\UserRepositoryInterface;
use Tailgate\Domain\Service\AbstractService;

class UpdateUserHandler extends AbstractService
{
    private $userRepository;
    private $passwordHashing;
    private $randomStringer;

    public function __construct(ValidatorInterface $validator, UserRepositoryInterface $userRepository)
    {
        parent::__construct($validator);
        $this->userRepository = $userRepository;
    }

    public function handle(UpdateUserCommand $command)
    {
        $this->validate($command);
        
        $userId = $command->getUserId();
        $email = $command->getEmail();
        $status = $command->getStatus();
        $role = $command->getRole();

        $user = $this->userRepository->get(UserId::fromString($userId));

        $user->update($email, $status, $role);

        $this->userRepository->add($user);
    }
}
