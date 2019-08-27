<?php

namespace Tailgate\Test\Domain\Model\Team;

use Buttercup\Protects\AggregateHistory;
use PHPUnit\Framework\TestCase;
use Tailgate\Domain\Model\Group\GroupId;
use Tailgate\Domain\Model\Team\Follow;
use Tailgate\Domain\Model\Team\FollowId;
use Tailgate\Domain\Model\Team\Team;
use Tailgate\Domain\Model\Team\TeamId;

class TeamTest extends TestCase
{
    private $teamId;
    private $designation = 'designation';
    private $mascot = 'mascot';

    public function setUp()
    {
        $this->teamId = TeamId::fromString('teamId');
    }

    public function testTeamShouldBeTheSameAfterReconstitution()
    {
        $team = Team::create($this->teamId, $this->designation, $this->mascot);
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
        $team = Team::create($this->teamId, $this->designation, $this->mascot);

        $this->assertEquals($this->teamId, $team->getId());
        $this->assertEquals($this->designation, $team->getDesignation());
        $this->assertEquals($this->mascot, $team->getMascot());
    }

    public function testFollowAddedWhenTeamIsFollowed()
    {
        $team = Team::create($this->teamId, $this->designation, $this->mascot);
        $groupId = GroupId::fromString('groupId');

        $team->followTeam($groupId);
        $followers = $team->getFollowers();

        $this->assertCount(1, $followers);
        $this->assertTrue($followers[0] instanceof Follow);
        $this->assertTrue($followers[0]->getFollowId() instanceof FollowId);
        $this->assertTrue($followers[0]->getGroupId()->equals($groupId));
        $this->assertTrue($followers[0]->getTeamId()->equals($this->teamId));
    }

    public function testATeamCanBeUpdated()
    {
        $designation = 'updatedDesignaton';
        $mascot = 'updatedMascot';
        $team = Team::create($this->teamId, $this->designation, $this->mascot);

        $team->update($designation, $mascot);

        $this->assertEquals($designation, $team->getDesignation());
        $this->assertEquals($mascot, $team->getMascot());
    }

    public function testATeamCanBeDeleted()
    {
        $team = Team::create($this->teamId, $this->designation, $this->mascot);

        $team->delete();

        // deleting a team does not affect anything on the entity yet but we need an assertion
        $this->assertTrue(true);
    }

    public function testAFollowCanBeDeleted()
    {
        // create a team, add two followers
        $team = Team::create($this->teamId, $this->designation, $this->mascot);
        $groupId1 = GroupId::fromString('groupId1');
        $groupId2 = GroupId::fromString('groupId2');
        $team->followTeam($groupId1);
        $team->followTeam($groupId2);

        // confirm there are two followers for the team
        $followers = $team->getFollowers();
        $this->assertCount(2, $followers);

        // get the two followerIds, delete one, confirm the other is still asociated o the team
        $followerId1 = $followers[0]->getFollowId();
        $followerId2 = $followers[1]->getFollowId();

        $team->deleteFollow($followerId1);

        $followers = $team->getFollowers();

        $this->assertCount(1, $followers);
        $this->assertTrue($followers[0]->getFollowId()->equals($followerId2));
    }
}
