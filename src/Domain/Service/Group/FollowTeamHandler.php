<?php

namespace Tailgate\Domain\Service\Group;

use Tailgate\Application\Command\Group\FollowTeamCommand;
use Tailgate\Domain\Model\Common\Date;
use Tailgate\Domain\Model\Group\GroupId;
use Tailgate\Domain\Model\Group\GroupRepositoryInterface;
use Tailgate\Domain\Model\Season\SeasonId;
use Tailgate\Domain\Model\Team\TeamId;
use Tailgate\Domain\Service\Clock\Clock;

class FollowTeamHandler
{
    private $clock;
    private $groupRepository;

    public function __construct(Clock $clock, GroupRepositoryInterface $groupRepository)
    {
        $this->clock = $clock;
        $this->groupRepository = $groupRepository;
    }

    public function handle(FollowTeamCommand $command)
    {
        $group = $this->groupRepository->get(GroupId::fromString($command->getGroupId()));

        $group->followTeam(
            TeamId::fromString($command->getTeamId()),
            SeasonId::fromString($command->getSeasonId()),
            Date::fromDateTimeImmutable($this->clock->currentTime())
        );

        $this->groupRepository->add($group);
    }
}
