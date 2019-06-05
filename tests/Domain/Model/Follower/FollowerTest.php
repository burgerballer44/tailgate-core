<?php

namespace Tailgate\Test\Domain\Model\Follower;

use Buttercup\Protects\AggregateHistory;
use PHPUnit\Framework\TestCase;
use Tailgate\Domain\Model\Follower\Follower;
use Tailgate\Domain\Model\Follower\FollowerId;
use Tailgate\Domain\Model\Group\GroupId;
use Tailgate\Domain\Model\Team\TeamId;

class FollowerTest extends TestCase
{
    private $followerId;
    private $groupId;
    private $teamId;

    public function setUp()
    {
        $this->followerId = FollowerId::fromString('followerId');
        $this->groupId = GroupId::fromString('groupId');
        $this->teamId = TeamId::fromString('groupId');
    }

    public function testFollowerShouldBeTheSameAfterReconstitution()
    {
        $follower = Follower::create($this->followerId, $this->groupId, $this->teamId);
        $events = $follower->getRecordedEvents();
        $follower->clearRecordedEvents();

        $reconstitutedFollower = Follower::reconstituteFrom(
            new AggregateHistory($this->followerId, (array) $events)
        );

        $this->assertEquals($follower, $reconstitutedFollower,
            'the reconstituted follower does not match the original follower object'
        );
    }

    public function testAFollowerCanBeCreated()
    {
        $follower = Follower::create($this->followerId, $this->groupId, $this->teamId);

        $this->assertEquals($this->followerId, $follower->getId());
        $this->assertEquals($this->groupId, $follower->getGroupId());
        $this->assertEquals($this->teamId, $follower->getTeamId());
    }
}
