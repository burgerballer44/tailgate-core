<?php

namespace Tailgate\Infrastructure\Service\DataTransformer;

use Tailgate\Domain\Service\DataTransformer\ScoreDataTransformerInterface;
use Tailgate\Domain\Model\Group\ScoreView;

class ScoreViewArrayDataTransformer implements ScoreDataTransformerInterface
{
    public function read(ScoreView $scoreView)
    {
        return [
            'scoreId' => $scoreView->getScoreId(),
            'groupId' => $scoreView->getGroupId(),
            'playerId' => $scoreView->getPlayerId(),
            'gameId' => $scoreView->getGameId(),
            'homeTeamPrediction' => $scoreView->getHomeTeamPrediction(),
            'awayTeamPrediction' => $scoreView->getAwayTeamPrediction(),
            'homeTeamId' => $scoreView->getHomeTeamId(),
            'awayTeamId' => $scoreView->getAwayTeamId(),
            'homeDesignation' => $scoreView->getHomeDesignation(),
            'homeMascot' => $scoreView->getHomeMascot(),
            'awayDesignation' => $scoreView->getAwayDesignation(),
            'awayMascot' => $scoreView->getAwayMascot(),
            'username' => $scoreView->getUsername()
        ];
    }
}
