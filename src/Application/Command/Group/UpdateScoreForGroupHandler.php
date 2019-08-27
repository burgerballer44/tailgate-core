<?php

namespace Tailgate\Application\Command\Group;

use Tailgate\Domain\Model\Season\GameId;
use Tailgate\Domain\Model\Group\GroupId;
use Tailgate\Domain\Model\Group\MemberId;
use Tailgate\Domain\Model\Group\ScoreId;
use Tailgate\Domain\Model\Group\GroupRepositoryInterface;

class UpdateScoreForGroupHandler
{
    public $groupRepository;

    public function __construct(GroupRepositoryInterface $groupRepository)
    {
        $this->groupRepository = $groupRepository;
    }

    public function handle(UpdateScoreForGroupCommand $command)
    {
        $groupId = $command->getGroupId();
        $scoreId = $command->getScoreId();
        $homeTeamPrediction = $command->getHomeTeamPrediction();
        $awayTeamPrediction = $command->getAwayTeamPrediction();

        $group = $this->groupRepository->get(GroupId::fromString($groupId));

        $group->updateScore(
            ScoreId::fromString($scoreId),
            $homeTeamPrediction,
            $awayTeamPrediction
        );

        $this->groupRepository->add($group);
    }
}
