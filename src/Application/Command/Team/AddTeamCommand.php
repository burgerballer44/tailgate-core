<?php

namespace Tailgate\Application\Command\Team;

class AddTeamCommand
{
    private $designation;
    private $mascot;
    private $sport;

    public function __construct($designation, $mascot, $sport)
    {
        $this->designation = $designation;
        $this->mascot = $mascot;
        $this->sport = $sport;
    }

    public function getDesignation()
    {
        return $this->designation;
    }

    public function getMascot()
    {
        return $this->mascot;
    }

    public function getSport()
    {
        return $this->sport;
    }
}
