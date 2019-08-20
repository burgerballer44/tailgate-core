<?php

namespace Tailgate\Application\Command\Group;

use Tailgate\Domain\Model\Season\GameId;
use Tailgate\Domain\Model\Group\GroupId;
use Tailgate\Domain\Model\Group\GroupRepositoryInterface;
use Tailgate\Domain\Model\User\UserId;

class SubmitScoreForGroupHandler
{
    public $groupRepository;

    public function __construct(GroupRepositoryInterface $groupRepository)
    {
        $this->groupRepository = $groupRepository;
    }

    public function handle(SubmitScoreForGroupCommand $command)
    {
        $groupId = $command->getGroupId();
        $userId = $command->getUserId();
        $gameId = $command->getGameId();
        $homeTeamPrediction = $command->getHomeTeamPrediction();
        $awayTeamPrediction = $command->getAwayTeamPrediction();

        $group = $this->groupRepository->get(GroupId::fromString($groupId));

        $group->submitScore(
            UserId::fromString($userId),
            GameId::fromString($gameId),
            $homeTeamPrediction,
            $awayTeamPrediction
        );

        $this->groupRepository->add($group);
    }
}
