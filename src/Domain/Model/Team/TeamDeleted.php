<?php

namespace Tailgate\Domain\Model\Team;

use Tailgate\Domain\Model\DomainEvent;

class TeamDeleted implements DomainEvent, TeamDomainEvent
{
    private $teamId;
    private $dateOccurred;

    public function __construct(TeamId $teamId, $dateOccurred)
    {
        $this->teamId = $teamId;
        $this->dateOccurred = $dateOccurred;
    }

    public function getEventDescription(): string
    {
        return 'Team removed.';
    }

    public function getAggregateId()
    {
        return $this->teamId;
    }

    public function getDateOccurred()
    {
        return (string) $this->dateOccurred;
    }
}
