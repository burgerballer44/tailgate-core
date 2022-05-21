<?php

namespace Tailgate\Domain\Service\Group;

use Tailgate\Application\Command\Group\SubmitScoreForGroupCommand;
use Tailgate\Domain\Model\Common\Date;
use Tailgate\Domain\Model\Group\GroupId;
use Tailgate\Domain\Model\Group\GroupRepositoryInterface;
use Tailgate\Domain\Model\Group\PlayerId;
use Tailgate\Domain\Model\Season\GameId;
use Tailgate\Domain\Service\Clock\Clock;

class SubmitScoreForGroupHandler
{
    private $clock;
    private $groupRepository;

    public function __construct(Clock $clock, GroupRepositoryInterface $groupRepository)
    {
        $this->clock = $clock;
        $this->groupRepository = $groupRepository;
    }

    public function handle(SubmitScoreForGroupCommand $command)
    {
        $group = $this->groupRepository->get(GroupId::fromString($command->getGroupId()));

        $group->submitScore(
            PlayerId::fromString($command->getPlayerId()),
            GameId::fromString($command->getGameId()),
            $command->getHomeTeamPrediction(),
            $command->getAwayTeamPrediction(),
            Date::fromDateTimeImmutable($this->clock->currentTime())
        );

        $this->groupRepository->add($group);
    }
}
