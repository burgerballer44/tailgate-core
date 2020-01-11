<?php

namespace Tailgate\Domain\Service\User;

use Tailgate\Application\Command\User\DeleteUserCommand;
use Tailgate\Application\Validator\ValidatorInterface;
use Tailgate\Domain\Model\User\User;
use Tailgate\Domain\Model\User\UserId;
use Tailgate\Domain\Model\User\UserRepositoryInterface;
use Tailgate\Domain\Service\AbstractService;

class DeleteUserHandler extends AbstractService
{
    private $userRepository;

    public function __construct(ValidatorInterface $validator, UserRepositoryInterface $userRepository)
    {
        parent::__construct($validator);
        $this->userRepository = $userRepository;
    }

    public function handle(DeleteUserCommand $command)
    {
        // $this->validate($command);
        
        $userId = $command->getUserId();

        $user = $this->userRepository->get(UserId::fromString($userId));

        $user->delete();

        $this->userRepository->add($user);
    }
}