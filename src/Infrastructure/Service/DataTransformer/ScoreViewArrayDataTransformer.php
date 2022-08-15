<?php

namespace Tailgate\Infrastructure\Service\DataTransformer;

use Tailgate\Domain\Model\Group\ScoreView;
use Tailgate\Domain\Service\DataTransformer\ScoreDataTransformerInterface;

class ScoreViewArrayDataTransformer implements ScoreDataTransformerInterface
{
    public function read(ScoreView $scoreView)
    {
        return [
            'score_id' => $scoreView->getScoreId(),
            'group_id' => $scoreView->getGroupId(),
            'player_id' => $scoreView->getPlayerId(),
            'game_id' => $scoreView->getGameId(),
            'home_team_prediction' => $scoreView->getHomeTeamPrediction(),
            'away_team_prediction' => $scoreView->getAwayTeamPrediction(),
            'home_team_id' => $scoreView->getHomeTeamId(),
            'away_team_id' => $scoreView->getAwayTeamId(),
            'home_designation' => $scoreView->getHomeDesignation(),
            'home_mascot' => $scoreView->getHomeMascot(),
            'away_designation' => $scoreView->getAwayDesignation(),
            'away_mascot' => $scoreView->getAwayMascot(),
            'username' => $scoreView->getUsername(),
        ];
    }
}
