<?php

namespace Tailgate\Application\Command\Group;

use Tailgate\Domain\Model\Group\Group;
use Tailgate\Domain\Model\Group\GroupId;
use Tailgate\Domain\Model\Group\MemberId;
use Tailgate\Domain\Model\Group\GroupRepositoryInterface;

class DeleteMemberHandler
{
    private $groupRepository;

    public function __construct(GroupRepositoryInterface $groupRepository)
    {
        $this->groupRepository = $groupRepository;
    }

    public function handle(DeleteMemberCommand $command)
    {
        $groupId = $command->getGroupId();
        $memberId = $command->getMemberId();

        $group = $this->groupRepository->get(GroupId::fromString($groupId));

        $group->deleteMember(MemberId::fromString($memberId));

        $this->groupRepository->add($group);
    }
}
