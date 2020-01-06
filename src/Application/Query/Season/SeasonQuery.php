<?php

namespace Tailgate\Application\Query\Season;

class SeasonQuery
{
    private $seasonId;

    public function __construct($seasonId)
    {
        $this->seasonId = $seasonId;
    }

    public function getSeasonId()
    {
        return $this->seasonId;
    }
}
