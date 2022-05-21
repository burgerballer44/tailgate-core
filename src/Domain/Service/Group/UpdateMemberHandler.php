<?php

namespace Tailgate\Domain\Service\Group;

use Tailgate\Application\Command\Group\UpdateMemberCommand;
use Tailgate\Domain\Model\Common\Date;
use Tailgate\Domain\Model\Group\GroupId;
use Tailgate\Domain\Model\Group\GroupRepositoryInterface;
use Tailgate\Domain\Model\Group\MemberId;
use Tailgate\Domain\Service\Clock\Clock;

class UpdateMemberHandler
{
    private $clock;
    private $groupRepository;

    public function __construct(Clock $clock, GroupRepositoryInterface $groupRepository)
    {
        $this->clock = $clock;
        $this->groupRepository = $groupRepository;
    }

    public function handle(UpdateMemberCommand $command)
    {
        $group = $this->groupRepository->get(GroupId::fromString($command->getGroupId()));

        $group->updateMember(
            MemberId::fromString($command->getMemberId()),
            $command->getGroupRole(),
            $command->getAllowMultiplePlayers(),
            Date::fromDateTimeImmutable($this->clock->currentTime())
        );

        $this->groupRepository->add($group);
    }
}
