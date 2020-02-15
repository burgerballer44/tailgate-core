<?php

namespace Tailgate\Domain\Service\Group;

use Tailgate\Application\Command\Group\AddMemberToGroupCommand;
use Tailgate\Application\Validator\ValidatorInterface;
use Tailgate\Domain\Model\Group\GroupId;
use Tailgate\Domain\Model\Group\GroupRepositoryInterface;
use Tailgate\Domain\Model\User\UserId;
use Tailgate\Domain\Service\Validatable;
use Tailgate\Domain\Service\ValidatableService;

class AddMemberToGroupHandler implements ValidatableService
{
    use Validatable;
    
    private $validator;
    private $groupRepository;

    public function __construct(ValidatorInterface $validator, GroupRepositoryInterface $groupRepository)
    {
        $this->validator = $validator;
        $this->groupRepository = $groupRepository;
    }

    public function handle(AddMemberToGroupCommand $command)
    {
        $this->validate($command);

        $groupId = $command->getGroupId();
        $userId = $command->getUserId();

        $group = $this->groupRepository->get(GroupId::fromString($groupId));

        $group->addMember(UserId::fromString($userId));

        $this->groupRepository->add($group);
    }
}
