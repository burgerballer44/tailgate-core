<?php

namespace Tailgate\Infrastructure\Service\DataTransformer;

use Tailgate\Domain\Model\Season\GameView;
use Tailgate\Domain\Service\DataTransformer\GameDataTransformerInterface;

class GameViewArrayDataTransformer implements GameDataTransformerInterface
{
    public function read(GameView $gameView)
    {
        return [
            'seasonId' => $gameView->getSeasonId(),
            'gameId' => $gameView->getGameId(),
            'homeTeamId' => $gameView->getHomeTeamId(),
            'awayTeamId' => $gameView->getAwayTeamId(),
            'homeTeamScore' => $gameView->getHomeTeamScore(),
            'awayTeamScore' => $gameView->getAwayTeamScore(),
            'startDate' => $gameView->getStartDate(),
            'startTime' => $gameView->getStartTime(),
            'homeDesignation' => $gameView->getHomeDesignation(),
            'homeMascot' => $gameView->getHomeMascot(),
            'awayDesignation' => $gameView->getAwayDesignation(),
            'awayMascot' => $gameView->getAwayMascot(),
        ];
    }
}
