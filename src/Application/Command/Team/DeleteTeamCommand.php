<?php

namespace Tailgate\Application\Command\Team;

class DeleteTeamCommand
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
