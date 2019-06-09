<?php

namespace Tailgate\Domain\Model\Team;

use Buttercup\Protects\DomainEvent;

class TeamAdded implements DomainEvent
{
    private $teamId;
    private $designation;
    private $mascot;
    private $occurredOn;

    public function __construct(TeamId $teamId, $designation, $mascot)
    {
        $this->teamId = $teamId;
        $this->designation = $designation;
        $this->mascot = $mascot;
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

    public function getOccurredOn()
    {
        return $this->occurredOn;
    }
}