<?php

namespace Tailgate\Application\Query\Team;

class TeamQuery
{
    private $teamId;

    public function __construct($teamId)
    {
        $this->teamId = $teamId;
    }

    public function getTeamId()
    {
        return $this->teamId;
    }
}
