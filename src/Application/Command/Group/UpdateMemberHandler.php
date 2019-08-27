<?php

namespace Tailgate\Application\Command\Group;

use Tailgate\Domain\Model\Season\GameId;
use Tailgate\Domain\Model\Group\MemberId;
use Tailgate\Domain\Model\Group\GroupId;
use Tailgate\Domain\Model\Group\GroupRepositoryInterface;

class UpdateMemberHandler
{
    public $groupRepository;

    public function __construct(GroupRepositoryInterface $groupRepository)
    {
        $this->groupRepository = $groupRepository;
    }

    public function handle(UpdateMemberCommand $command)
    {
        $groupId = $command->getGroupId();
        $memberId = $command->getMemberId();
        $groupRole = $command->getGroupRole();

        $group = $this->groupRepository->get(GroupId::fromString($groupId));

        $group->updateMember(
            MemberId::fromString($memberId),
            $groupRole
        );

        $this->groupRepository->add($group);
    }
}
