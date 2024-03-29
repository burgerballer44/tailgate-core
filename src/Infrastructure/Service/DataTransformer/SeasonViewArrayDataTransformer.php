<?php

namespace Tailgate\Infrastructure\Service\DataTransformer;

use Tailgate\Domain\Model\Season\SeasonView;
use Tailgate\Domain\Service\DataTransformer\GameDataTransformerInterface;
use Tailgate\Domain\Service\DataTransformer\SeasonDataTransformerInterface;

class SeasonViewArrayDataTransformer implements SeasonDataTransformerInterface
{
    private $gameViewTransformer;

    public function __construct(GameDataTransformerInterface $gameViewTransformer)
    {
        $this->gameViewTransformer = $gameViewTransformer;
    }

    public function read(SeasonView $seasonView)
    {
        $games = [];

        foreach ($seasonView->getGames() as $gameView) {
            $games[] = $this->gameViewTransformer->read($gameView);
        }

        return [
            'season_id' => $seasonView->getSeasonId(),
            'sport' => $seasonView->getSport(),
            'season_type' => $seasonView->getSeasonType(),
            'name' => $seasonView->getName(),
            'season_start' => $seasonView->getSeasonStart(),
            'season_end' => $seasonView->getSeasonEnd(),
            'games' => $games,
        ];
    }
}
