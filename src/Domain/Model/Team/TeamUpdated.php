<?php

namespace Tailgate\Domain\Model\Team;

use Tailgate\Domain\Model\Common\Date;
use Tailgate\Domain\Model\DomainEvent;

class TeamUpdated implements DomainEvent, TeamDomainEvent
{
    private $teamId;
    private $designation;
    private $mascot;
    private $dateOccurred;

    public function __construct(TeamId $teamId, $designation, $mascot, Date $dateOccurred)
    {
        $this->teamId = $teamId;
        $this->designation = $designation;
        $this->mascot = $mascot;
        $this->dateOccurred = $dateOccurred;
    }

    public function getEventDescription(): string
    {
        return 'Team information updated.';
    }

    public function getAggregateId()
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

    public function getDateOccurred()
    {
        return (string) $this->dateOccurred;
    }
}
