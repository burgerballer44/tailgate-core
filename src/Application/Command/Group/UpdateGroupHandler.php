<?php

namespace Tailgate\Application\Command\Group;

use Tailgate\Domain\Model\User\UserId;
use Tailgate\Domain\Model\Group\GroupId;
use Tailgate\Domain\Model\Group\GroupRepositoryInterface;

class UpdateGroupHandler
{
    public $groupRepository;

    public function __construct(GroupRepositoryInterface $groupRepository)
    {
        $this->groupRepository = $groupRepository;
    }

    public function handle(UpdateGroupCommand $command)
    {
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
