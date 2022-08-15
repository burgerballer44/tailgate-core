<?php

namespace Tailgate\Infrastructure\Service\DataTransformer;

use Tailgate\Domain\Model\Season\GameView;
use Tailgate\Domain\Service\DataTransformer\GameDataTransformerInterface;

class GameViewArrayDataTransformer implements GameDataTransformerInterface
{
    public function read(GameView $gameView)
    {
        return [
            'season_id' => $gameView->getSeasonId(),
            'game_id' => $gameView->getGameId(),
            'homeTeam_id' => $gameView->getHomeTeamId(),
            'awayTeam_id' => $gameView->getAwayTeamId(),
            'home_team_score' => $gameView->getHomeTeamScore(),
            'away_team_score' => $gameView->getAwayTeamScore(),
            'start_date' => $gameView->getStartDate(),
            'start_time' => $gameView->getStartTime(),
            'home_designation' => $gameView->getHomeDesignation(),
            'home_mascot' => $gameView->getHomeMascot(),
            'away_designation' => $gameView->getAwayDesignation(),
            'away_mascot' => $gameView->getAwayMascot(),
        ];
    }
}
