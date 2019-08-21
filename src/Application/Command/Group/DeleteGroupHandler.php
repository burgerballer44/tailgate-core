<?php

namespace Tailgate\Application\Command\Group;

use Tailgate\Domain\Model\Group\Group;
use Tailgate\Domain\Model\Group\GroupId;
use Tailgate\Domain\Model\Group\GroupRepositoryInterface;

class DeleteGroupHandler
{
    private $groupRepository;

    public function __construct(GroupRepositoryInterface $groupRepository)
    {
        $this->groupRepository = $groupRepository;
    }

    public function handle(DeleteGroupCommand $command)
    {
        $groupId = $command->getGroupId();

        $group = $this->groupRepository->get(GroupId::fromString($groupId));

        $group->delete();

        $this->groupRepository->add($group);
    }
}
