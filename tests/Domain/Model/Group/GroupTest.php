<?php

namespace Tailgate\Test\Domain\Model\User;

use Buttercup\Protects\AggregateHistory;
use PHPUnit\Framework\TestCase;
use Tailgate\Domain\Model\Group\Group;
use Tailgate\Domain\Model\Group\GroupId;
use Tailgate\Domain\Model\Group\Follow;
use Tailgate\Domain\Model\Group\FollowId;
use Tailgate\Domain\Model\Group\Member;
use Tailgate\Domain\Model\Group\MemberId;
use Tailgate\Domain\Model\Group\Score;
use Tailgate\Domain\Model\Group\ScoreId;
use Tailgate\Domain\Model\Game\GameId;
use Tailgate\Domain\Model\User\UserId;
use Tailgate\Domain\Model\Team\TeamId;

class GroupTest extends TestCase
{
    private $groupId;
    private $groupName;
    private $ownerId;

    public function setUp()
    {
        $this->groupId = GroupId::fromString('groupId');
        $this->groupName = 'groupName';
        $this->ownerId = UserId::fromString('ownerId');
    }

    public function testGroupShouldBeTheSameAfterReconstitution()
    {
        $group = Group::create($this->groupId, $this->groupName, $this->ownerId);
        $events = $group->getRecordedEvents();
        $group->clearRecordedEvents();

        $reconstitutedGroup = Group::reconstituteFrom(
            new AggregateHistory($this->groupId, (array) $events)
        );

        $this->assertEquals($group, $reconstitutedGroup,
            'the reconstituted group does not match the original group'
        );
    }

    public function testAGroupCanBeCreated()
    {
        $group = Group::create($this->groupId, $this->groupName, $this->ownerId);

        $this->assertEquals($this->groupId, $group->getId());
        $this->assertEquals($this->groupName, $group->getName());
        $this->assertEquals($this->ownerId, $group->getOwnerId());
        $this->assertCount(1, $group->getMembers());
        $this->assertEmpty($group->getScores());
    }

    public function testUserWhoCreatesGroupIsAMemberOfGroupWhenGroupIsCreated()
    {
        $group = Group::create($this->groupId, $this->groupName, $this->ownerId);
        $members = $group->getMembers();

        $this->assertCount(1, $members);
        $this->assertTrue($members[0] instanceof Member);
        $this->assertTrue($members[0]->getMemberId() instanceof MemberId);
        $this->assertTrue($members[0]->getGroupId()->equals($this->groupId));
        $this->assertTrue($members[0]->getUserId()->equals($this->ownerId));
        $this->assertEquals($members[0]->getGroupRole(), Group::G_ROLE_ADMIN);
    }

    public function testGroupHasAScoreWhenScoreIsSubmitted()
    {
        $group = Group::create($this->groupId, $this->groupName, $this->ownerId);
        $userId = UserId::fromString('userID');
        $gameId = GameId::fromString('gameID');
        $homeTeamPrediction = '70';
        $awayTeamPrediction = '60';

        $group->submitScore($this->groupId, $userId, $gameId, $homeTeamPrediction, $awayTeamPrediction);
        $scores = $group->getScores();

        $this->assertCount(1, $scores);
        $this->assertTrue($scores[0] instanceof Score);
        $this->assertTrue($scores[0]->getScoreId() instanceof ScoreId);
        $this->assertTrue($scores[0]->getGroupId()->equals($this->groupId));
        $this->assertTrue($scores[0]->getUserId()->equals($userId));
        $this->assertTrue($scores[0]->getGameId()->equals($gameId));
        $this->assertEquals($homeTeamPrediction, $scores[0]->getHomeTeamPrediction());
        $this->assertEquals($awayTeamPrediction, $scores[0]->getAwayTeamPrediction());
    }

    public function testGroupGetsAMemberWhenMemberIsAdded()
    {
        $group = Group::create($this->groupId, $this->groupName, $this->ownerId);
        $userId = UserId::fromString('userID');

        $group->addMember($this->groupId, $userId);
        $members = $group->getMembers();

        // 2 because of the owner and new member
        $this->assertCount(2, $members);
        $this->assertTrue($members[1] instanceof Member);
        $this->assertTrue($members[1]->getMemberId() instanceof MemberId);
        $this->assertTrue($members[1]->getGroupId()->equals($this->groupId));
        $this->assertTrue($members[1]->getUserId()->equals($userId));
        $this->assertEquals($members[1]->getGroupRole(), Group::G_ROLE_MEMBER);
    }

    public function testFollowAddedWhenTeamIsFollowed()
    {
        $group = Group::create($this->groupId, $this->groupName, $this->ownerId);
        $teamId = TeamId::fromString('teamId');

        $group->followTeam($this->groupId, $teamId);
        $follows = $group->getFollows();

        $this->assertCount(1, $follows);
        $this->assertTrue($follows[0] instanceof Follow);
        $this->assertTrue($follows[0]->getFollowId() instanceof FollowId);
        $this->assertTrue($follows[0]->getGroupId()->equals($this->groupId));
        $this->assertTrue($follows[0]->getTeamId()->equals($teamId));
    }
}
