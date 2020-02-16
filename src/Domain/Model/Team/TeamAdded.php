<?php

namespace Tailgate\Domain\Model\Team;

use Buttercup\Protects\DomainEvent;

class TeamAdded implements DomainEvent, TeamDomainEvent
{
    private $teamId;
    private $designation;
    private $mascot;
    private $sport;
    private $occurredOn;

    public function __construct(TeamId $teamId, $designation, $mascot, $sport)
    {
        $this->teamId = $teamId;
        $this->designation = $designation;
        $this->mascot = $mascot;
        $this->sport = $sport;
        $this->occurredOn = new \DateTimeImmutable();
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

    public function getOccurredOn()
    {
        return $this->occurredOn;
    }
}
