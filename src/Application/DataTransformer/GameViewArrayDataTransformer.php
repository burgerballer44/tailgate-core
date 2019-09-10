<?php

namespace Tailgate\Application\DataTransformer;

use Tailgate\Application\DataTransformer\GameDataTransformerInterface;
use Tailgate\Domain\Model\Season\GameView;

class GameViewArrayDataTransformer implements GameDataTransformerInterface
{
    public function read(GameView $gameView)
    {
        return [
            'seasonId'      => $gameView->getSeasonId(),
            'gameId'        => $gameView->getGameId(),
            'homeTeamId'    => $gameView->getHomeTeamId(),
            'awayTeamId'    => $gameView->getAwayTeamId(),
            'homeTeamScore' => $gameView->getHomeTeamScore(),
            'awayTeamScore' => $gameView->getAwayTeamScore(),
            'startDate'     => $gameView->getStartDate(),
        ];
    }
}
