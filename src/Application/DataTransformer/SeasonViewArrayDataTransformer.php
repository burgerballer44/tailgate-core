<?php

namespace Tailgate\Application\DataTransformer;

use Tailgate\Application\DataTransformer\SeasonDataTransformerInterface;
use Tailgate\Domain\Model\Season\SeasonView;

class SeasonViewArrayDataTransformer implements SeasonDataTransformerInterface
{
    public function read(SeasonView $seasonView)
    {
        return [
            'seasonId'    => $seasonView->getSeasonId(),
            'sport'       => $seasonView->getSport(),
            'type'        => $seasonView->getType(),
            'name'        => $seasonView->getName(),
            'seasonStart' => $seasonView->getSeasonStart(),
            'seasonEnd'   => $seasonView->getSeasonEnd(),
        ];
    }
}