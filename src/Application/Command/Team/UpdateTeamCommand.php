<?php

namespace Tailgate\Application\Command\Team;

class UpdateTeamCommand
{
    private $teamId;
    private $designation;
    private $mascot;

    public function __construct($teamId, $designation, $mascot)
    {
        $this->teamId = $teamId;
        $this->designation = $designation;
        $this->mascot = $mascot;
    }

    public function getTeamId()
    {
        return $this->teamId;
    }

    public function getDesignation()
    {
        return $this->designation;
    }

    public function getMascot()
    {
        return $this->mascot;
    }
}
