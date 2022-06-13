<?php

namespace Tailgate\Domain\Service\Group;

use Tailgate\Application\Command\Group\AddMemberToGroupCommand;
use Tailgate\Domain\Model\Common\Date;
use Tailgate\Domain\Model\Group\GroupId;
use Tailgate\Domain\Model\Group\GroupRepositoryInterface;
use Tailgate\Domain\Model\User\UserId;
use Tailgate\Domain\Service\Clock\Clock;

class AddMemberToGroupHandler
{
    private $clock;
    private $groupRepository;

    public function __construct(Clock $clock, GroupRepositoryInterface $groupRepository)
    {
        $this->clock = $clock;
        $this->groupRepository = $groupRepository;
    }

    public function handle(AddMemberToGroupCommand $command)
    {
        $group = $this->groupRepository->get(GroupId::fromString($command->getGroupId()));

        $group->addMember(
            UserId::fromString($command->getUserId()),
            Date::fromDateTimeImmutable($this->clock->currentTime())
        );

        $this->groupRepository->add($group);
    }
}
