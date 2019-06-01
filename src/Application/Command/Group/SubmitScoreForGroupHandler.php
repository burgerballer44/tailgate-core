<?php

namespace Tailgate\Application\Command\Group;

use Tailgate\Domain\Model\Game\GameId;
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

    public function handle(SubmitScoreForGroupCommand $submitScoreForGroupCommand)
    {
        $groupId = $submitScoreForGroupCommand->getGroupId();
        $userId = $submitScoreForGroupCommand->getUserId();
        $gameId = $submitScoreForGroupCommand->getGameId();
        $homeTeamPrediction = $submitScoreForGroupCommand->getHomeTeamPrediction();
        $awayTeamPrediction = $submitScoreForGroupCommand->getAwayTeamPrediction();

        $group = $this->groupRepository->get(GroupID::fromString($groupId));

        // cannot submit another score
        // cannot submit score if game has started
        // cannot submit score someone else has

        $group->submitScore(
            GroupId::fromString($groupId),
            UserId::fromString($userId),
            GameId::fromString($gameId),
            $homeTeamPrediction,
            $awayTeamPrediction
        );

        $this->groupRepository->add($group);
    }
}