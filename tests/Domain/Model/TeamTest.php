<?php

namespace Tailgate\Test\Domain\Model;

use Buttercup\Protects\AggregateHistory;
use PHPUnit\Framework\TestCase;
use Tailgate\Domain\Model\Group\GroupId;
use Tailgate\Domain\Model\Season\Season;
use Tailgate\Domain\Model\Season\SeasonId;
use Tailgate\Domain\Model\Team\Follow;
use Tailgate\Domain\Model\Team\FollowId;
use Tailgate\Domain\Model\Team\Team;
use Tailgate\Domain\Model\Team\TeamId;

class TeamTest extends TestCase
{
    private $teamId;
    private $designation = 'designation';
    private $mascot = 'mascot';
    private $sport = Season::SPORT_FOOTBALL;

    public function setUp(): void
    {
        $this->teamId = TeamId::fromString('teamId');
    }

    public function testTeamShouldBeTheSameAfterReconstitution()
    {
        $team = Team::create($this->teamId, $this->designation, $this->mascot, $this->sport);
        $events = $team->getRecordedEvents();
        $team->clearRecordedEvents();

        $reconstitutedTeam = Team::reconstituteFrom(
            new AggregateHistory($this->teamId, (array) $events)
        );

        $this->assertEquals(
            $team,
            $reconstitutedTeam,
            'the reconstituted team does not match the original team'
        );
    }

    public function testATeamCanBeCreated()
    {
        $team = Team::create($this->teamId, $this->designation, $this->mascot, $this->sport);

        $this->assertEquals($this->teamId, $team->getId());
        $this->assertEquals($this->designation, $team->getDesignation());
        $this->assertEquals($this->mascot, $team->getMascot());
    }

    public function testATeamCanBeUpdated()
    {
        $designation = 'updatedDesignaton';
        $mascot = 'updatedMascot';
        $team = Team::create($this->teamId, $this->designation, $this->mascot, $this->sport);

        $team->update($designation, $mascot);

        $this->assertEquals($designation, $team->getDesignation());
        $this->assertEquals($mascot, $team->getMascot());
    }
}
