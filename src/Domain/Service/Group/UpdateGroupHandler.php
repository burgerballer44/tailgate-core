<?php

namespace Tailgate\Domain\Service\Group;

use Tailgate\Application\Command\Group\UpdateGroupCommand;
use Tailgate\Application\Validator\ValidatorInterface;
use Tailgate\Domain\Model\Group\GroupId;
use Tailgate\Domain\Model\Group\GroupRepositoryInterface;
use Tailgate\Domain\Model\User\UserId;
use Tailgate\Domain\Service\Validatable;
use Tailgate\Domain\Service\ValidatableService;

class UpdateGroupHandler implements ValidatableService
{
    use Validatable;
    
    private $validator;
    private $groupRepository;

    public function __construct(ValidatorInterface $validator, GroupRepositoryInterface $groupRepository)
    {
        $this->validator = $validator;
        $this->groupRepository = $groupRepository;
    }

    public function handle(UpdateGroupCommand $command)
    {
        $this->validate($command);

        $groupId = $command->getGroupId();
        $name = $command->getName();
        $ownerId = $command->getOwnerId();

        $group = $this->groupRepository->get(GroupId::fromString($groupId));

        $group->update(
            $name,
            UserId::fromString($ownerId)
        );

        $this->groupRepository->add($group);
    }
}
