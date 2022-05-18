<?php

namespace Tailgate\Test\Domain\Model;

use Burger\Aggregate\AggregateHistory;
use Tailgate\Domain\Model\Common\Date;
use Tailgate\Domain\Model\Season\Sport;
use Tailgate\Domain\Model\Team\Team;
use Tailgate\Domain\Model\Team\TeamId;
use Tailgate\Test\BaseTestCase;

class TeamTest extends BaseTestCase
{
    private function createTeam()
    {
        return Team::create($this->teamId, $this->designation, $this->mascot, $this->sport, $this->dateOccurred);
    }

    public function setUp(): void
    {
        $this->teamId = TeamId::fromString('teamId');
        $this->designation = 'designation';
        $this->mascot = 'mascot';
        $this->sport = Sport::getFootball();
        $this->dateOccurred = Date::fromDateTimeImmutable($this->getFakeTime()->currentTime());
    }

    public function testTeamShouldBeTheSameAfterReconstitution()
    {
        // create team
        $team = $this->createTeam();
        $events = $team->getRecordedEvents();
        $team->clearRecordedEvents();

        // recreate the team using event array
        $reconstitutedTeam = Team::reconstituteFromEvents(
            new AggregateHistory($this->teamId, (array) $events)
        );

        // both team objects should be the same
        $this->assertEquals(
            $team,
            $reconstitutedTeam,
            'the reconstituted team does not match the original team'
        );
    }

    public function testATeamCanBeAdded()
    {
        $team = $this->createTeam();

        $teamAddedEvent = $team->getRecordedEvents()[0];

        $this->assertEquals($this->teamId, $teamAddedEvent->getAggregateId());
        $this->assertEquals($this->designation, $teamAddedEvent->getDesignation());
        $this->assertEquals($this->mascot, $teamAddedEvent->getMascot());
        $this->assertEquals($this->dateOccurred, $teamAddedEvent->getDateOccurred());
        $this->assertEquals($this->teamId, $team->getTeamId());
        $this->assertEquals($this->designation, $team->getDesignation());
        $this->assertEquals($this->mascot, $team->getMascot());
    }

    public function testDesignationAndMascotCanBeUpdated()
    {
        $team = $this->createTeam();
        $team->clearRecordedEvents();

        $designation = 'updatedDesignaton';
        $mascot = 'updatedMascot';
        $team->update($designation, $mascot, $this->dateOccurred);
        $teamUpdatedEvent = $team->getRecordedEvents()[0];

        $this->assertEquals($designation, $teamUpdatedEvent->getDesignation());
        $this->assertEquals($mascot, $teamUpdatedEvent->getMascot());
        $this->assertEquals($this->dateOccurred, $teamUpdatedEvent->getDateOccurred());
        $this->assertEquals($designation, $team->getDesignation());
        $this->assertEquals($mascot, $team->getMascot());
    }
}
