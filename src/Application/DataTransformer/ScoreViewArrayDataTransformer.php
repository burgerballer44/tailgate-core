<?php

namespace Tailgate\Application\DataTransformer;

use Tailgate\Application\DataTransformer\ScoreDataTransformerInterface;
use Tailgate\Domain\Model\Group\GroupView;

class ScoreViewArrayDataTransformer implements ScoreDataTransformerInterface
{
    public function read(ScoreView $scoreView)
    {
        return [
            'scoreId' => $scoreView->getScoreId(),
            'groupId' => $scoreView->getGroupId(),
            'userId' => $scoreView->getUserId(),
            'gameId' => $scoreView->getGameId(),
            'homeTeamPrediction' => $scoreView->getHomeTeamPrediction(),
            'awayTeamPrediction' => $scoreView->getAwayTeamPrediction()
        ];
    }
}