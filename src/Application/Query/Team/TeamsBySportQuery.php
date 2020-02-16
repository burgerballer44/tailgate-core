<?php

namespace Tailgate\Application\Query\Team;

class TeamsBySportQuery
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
