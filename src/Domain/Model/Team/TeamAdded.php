<?php

namespace Tailgate\Domain\Model\Team;

use Tailgate\Domain\Model\DomainEvent;

class TeamAdded implements DomainEvent, TeamDomainEvent
{
    private $teamId;
    private $designation;
    private $mascot;
    private $sport;
    private $dateOccurred;

    public function __construct(TeamId $teamId, $designation, $mascot, $sport, $dateOccurred)
    {
        $this->teamId = $teamId;
        $this->designation = $designation;
        $this->mascot = $mascot;
        $this->sport = $sport;
        $this->dateOccurred = $dateOccurred;
    }

    public function getEventDescription() : string
    {
        return 'Team added.';
    }

    public function getAggregateId()
    {
        return $this->teamId;
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

    public function getSport()
    {
        return $this->sport;
    }

    public function getDateOccurred()
    {
        return (string) $this->dateOccurred;
    }
}
