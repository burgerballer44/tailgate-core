<?php

namespace Tailgate\Application\Command\Group;

use Tailgate\Domain\Model\Group\GroupId;
use Tailgate\Domain\Model\Group\GroupRepositoryInterface;
use Tailgate\Domain\Model\Group\MemberId;

class AddPlayerToGroupHandler
{
    public $groupRepository;

    public function __construct(GroupRepositoryInterface $groupRepository)
    {
        $this->groupRepository = $groupRepository;
    }

    public function handle(AddPlayerToGroupCommand $command)
    {
        $groupId = $command->getGroupId();
        $memberId = $command->getMemberId();
        $username = $command->getUsername();

        $group = $this->groupRepository->get(GroupId::fromString($groupId));

        $group->addPlayer(MemberId::fromString($memberId), $username);

        $this->groupRepository->add($group);
    }
}
