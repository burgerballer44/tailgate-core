<?php

namespace Tailgate\Domain\Service\DataTransformer;

use Tailgate\Domain\Service\DataTransformer\SeasonDataTransformerInterface;
use Tailgate\Domain\Service\DataTransformer\GameDataTransformerInterface;
use Tailgate\Domain\Model\Season\SeasonView;

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
            'seasonId'    => $seasonView->getSeasonId(),
            'sport'       => $seasonView->getSport(),
            'seasonType'  => $seasonView->getSeasonType(),
            'name'        => $seasonView->getName(),
            'seasonStart' => $seasonView->getSeasonStart(),
            'seasonEnd'   => $seasonView->getSeasonEnd(),
            'games'       => $games,
        ];
    }
}
