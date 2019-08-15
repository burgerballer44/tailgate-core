<?php

namespace Tailgate\Application\Command\Group;

use Tailgate\Domain\Model\Group\GroupId;
use Tailgate\Domain\Model\Group\GroupRepositoryInterface;
use Tailgate\Domain\Model\User\UserId;

class AddMemberToGroupHandler
{
    public $groupRepository;

    public function __construct(GroupRepositoryInterface $groupRepository)
    {
        $this->groupRepository = $groupRepository;
    }

    public function handle(AddMemberToGroupCommand $addMemberToGroupCommand)
    {
        $groupId = $addMemberToGroupCommand->getGroupId();
        $userId = $addMemberToGroupCommand->getUserId();

        $group = $this->groupRepository->get(GroupId::fromString($groupId));

        $group->addMember(UserId::fromString($userId));

        $this->groupRepository->add($group);
    }
}
