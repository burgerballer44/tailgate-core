<?php

namespace Tailgate\Application\Query\Season;

class AllSeasonsBySportQuery
{
    private $sport;

    public function __construct($sport)
    {
        $this->sport = $sport;
    }

    public function getSport()
    {
        return $this->sport;
    }
}
