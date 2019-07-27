<?php

namespace Tailgate\Application\DataTransformer;

use Tailgate\Application\DataTransformer\GroupDataTransformerInterface;
use Tailgate\Domain\Model\Group\GroupView;

class ScoreViewArrayDataTransformer implements GroupDataTransformerInterface
{
    public function read(GroupView $groupView)
    {
        return [
            'scoreId' => $groupView->getScoreId(),
            'groupId' => $groupView->getGroupId(),
            'userId' => $groupView->getUserId(),
            'gameId' => $groupView->getGameId(),
            'homeTeamPrediction' => $groupView->getHomeTeamPrediction(),
            'awayTeamPrediction' => $groupView->getAwayTeamPrediction()
        ];
    }
}