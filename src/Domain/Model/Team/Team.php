<?php

namespace Tailgate\Domain\Model\Team;

use Burger\Aggregate\IdentifiesAggregate;
use Tailgate\Domain\Model\AbstractEventBasedEntity;
use Tailgate\Domain\Model\Common\Date;
use Tailgate\Domain\Model\Season\Sport;

class Team extends AbstractEventBasedEntity
{
    private $teamId;
    private $designation;
    private $mascot;
    private $sport;

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

    // create an empty team
    protected static function createEmptyEntity(IdentifiesAggregate $teamId)
    {
        return new static();
    }

    // create a team
    public static function create(TeamId $teamId, $designation, $mascot, Sport $sport, Date $dateOccurred)
    {
        $team = new static();

        $team->applyAndRecordThat(new TeamAdded($teamId, $designation, $mascot, $sport, $dateOccurred));

        return $team;
    }

    // updates the team's designation adn mascot
    public function update($designation, $mascot, Date $dateOccurred)
    {
        $this->applyAndRecordThat(new TeamUpdated($this->teamId, $designation, $mascot, $dateOccurred));
    }

    // delete team
    public function delete(Date $dateOccurred)
    {
        $this->applyAndRecordThat(new TeamDeleted($this->teamId, $dateOccurred));
    }

    protected function applyTeamAdded(TeamAdded $event)
    {
        $this->teamId = $event->getAggregateId();
        $this->designation = $event->getDesignation();
        $this->mascot = $event->getMascot();
        $this->sport = $event->getSport();
    }

    protected function applyTeamUpdated(TeamUpdated $event)
    {
        $this->designation = $event->getDesignation();
        $this->mascot = $event->getMascot();
    }

    protected function applyTeamDeleted()
    {
    }
}
