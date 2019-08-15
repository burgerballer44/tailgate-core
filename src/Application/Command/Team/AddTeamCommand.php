<?php

namespace Tailgate\Application\Command\Team;

class AddTeamCommand
{
    private $designation;
    private $mascot;

    public function __construct(string $designation, string $mascot)
    {
        $this->designation = $designation;
        $this->mascot = $mascot;
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
