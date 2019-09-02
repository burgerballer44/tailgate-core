<?php

namespace Tailgate\Test\Domain\Model\User;

use Buttercup\Protects\AggregateHistory;
use PHPUnit\Framework\TestCase;
use Tailgate\Domain\Model\Group\Group;
use Tailgate\Domain\Model\Group\GroupId;
use Tailgate\Domain\Model\Group\Member;
use Tailgate\Domain\Model\Group\MemberId;
use Tailgate\Domain\Model\Group\Player;
use Tailgate\Domain\Model\Group\PlayerId;
use Tailgate\Domain\Model\Group\Score;
use Tailgate\Domain\Model\Group\ScoreId;
use Tailgate\Domain\Model\Season\GameId;
use Tailgate\Domain\Model\User\UserId;

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

        $this->assertEquals(
            $group,
            $reconstitutedGroup,
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

        $group->submitScore($userId, $gameId, $homeTeamPrediction, $awayTeamPrediction);
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

        $group->addMember($userId);
        $members = $group->getMembers();

        // 2 because of the owner and new member
        $this->assertCount(2, $members);
        $this->assertTrue($members[1] instanceof Member);
        $this->assertTrue($members[1]->getMemberId() instanceof MemberId);
        $this->assertTrue($members[1]->getGroupId()->equals($this->groupId));
        $this->assertTrue($members[1]->getUserId()->equals($userId));
        $this->assertEquals($members[1]->getGroupRole(), Group::G_ROLE_MEMBER);
        $this->assertEquals($members[1]->getAllowMultiplePlayers(), Group::SINGLE_PLAYER);
    }

    public function testThereAreNoMembersAndScoresWhenGroupIsDeleted()
    {
        $group = Group::create($this->groupId, $this->groupName, $this->ownerId);
        $userId = UserId::fromString('userID');
        $gameId = GameId::fromString('gameID');
        $homeTeamPrediction = '70';
        $awayTeamPrediction = '60';
        $group->addMember($userId);
        $group->submitScore($userId, $gameId, $homeTeamPrediction, $awayTeamPrediction);

        $group->delete();

        $scores = $group->getScores();
        $members = $group->getMembers();

        $this->assertCount(0, $members);
        $this->assertCount(0, $scores);
    }

    public function testAMemberIsRemovedWhenAMemberIsDeleted()
    {
        $group = Group::create($this->groupId, $this->groupName, $this->ownerId);
        $userId2 = UserId::fromString('userId2');
        $userId3 = UserId::fromString('userId3');
        $group->addMember($userId2);
        $group->addMember($userId3);
        $members = $group->getMembers();
        $this->assertCount(3, $members);

        $memberId2 = $members[1]->getMemberId();
        $memberId3 = $members[2]->getMemberId();

        $group->deleteMember($memberId3);

        $members = $group->getMembers();

        $this->assertCount(2, $members);
        $this->assertTrue($members[1]->getMemberId()->equals($memberId2));
    }

    public function testAScoreIsRemovedWhenAScoreIsDeleted()
    {
        $group = Group::create($this->groupId, $this->groupName, $this->ownerId);
        $gameId = GameId::fromString('gameID');
        $homeTeamPrediction = '70';
        $awayTeamPrediction = '60';
        $group->submitScore($this->ownerId, $gameId, $homeTeamPrediction, $awayTeamPrediction);
        $scores = $group->getScores();
        $this->assertCount(1, $scores);

        $scoreId = $scores[0]->getScoreId();

        $group->deleteScore($scoreId);

        $scores = $group->getScores();

        $this->assertCount(0, $scores);
    }

    public function testAGroupCanBeUpdated()
    {
        $groupName = 'updatedgroupName';
        $ownerId = UserId::fromString('updatedownerId');

        $group = Group::create($this->groupId, $this->groupName, $this->ownerId);

        $group->update($groupName, $ownerId);

        $this->assertEquals($this->groupId, $group->getId());
        $this->assertEquals($groupName, $group->getName());
        $this->assertEquals($ownerId, $group->getOwnerId());
        $this->assertCount(1, $group->getMembers());
        $this->assertEmpty($group->getScores());
    }

    public function testAMemberCanBeUpdated()
    {
        $groupRole = 'updatedgroupRole';
        $allowMultiplePlayers = 'updatedAllowMultiplePlayers';
        $group = Group::create($this->groupId, $this->groupName, $this->ownerId);

        $members = $group->getMembers();
        $this->assertNotEquals($groupRole, $members[0]->getGroupRole());
        $this->assertNotEquals($allowMultiplePlayers, $members[0]->getAllowMultiplePlayers());

        $memberId = $group->getMembers()[0]->getMemberId();
        $group->updateMember($memberId, $groupRole, $allowMultiplePlayers);

        $members = $group->getMembers();
        $this->assertEquals($this->groupId, $members[0]->getGroupId());
        $this->assertEquals($groupRole, $members[0]->getGroupRole());
        $this->assertEquals($allowMultiplePlayers, $members[0]->getAllowMultiplePlayers());
    }

    public function testAScoreCanBeUpdated()
    {
        $homeTeamPrediction = '700';
        $awayTeamPrediction = '600';
        $group = Group::create($this->groupId, $this->groupName, $this->ownerId);
        $gameId = GameId::fromString('gameID');
        $group->submitScore($this->ownerId, $gameId, 1, 2);
        $scores = $group->getScores();
        $scoreId = $scores[0]->getScoreId();


        $group->updateScore($scoreId, $homeTeamPrediction, $awayTeamPrediction);

        $scores = $group->getScores();

        $this->assertEquals($homeTeamPrediction, $scores[0]->getHomeTeamPrediction());
        $this->assertEquals($awayTeamPrediction, $scores[0]->getAwayTeamPrediction());
    }

    public function testGroupGetsAPlayerWhenPlayerIsAdded()
    {   
        $username = 'username';
        $group = Group::create($this->groupId, $this->groupName, $this->ownerId);
        $members = $group->getMembers();
        $players = $group->getPlayers();
        $this->assertCount(0, $players);

        $memberId = $members[0]->getMemberId();

        $group->addPlayer($memberId, $username);

        $players = $group->getPlayers();
        $this->assertCount(1, $players);
        $this->assertTrue($players[0] instanceof Player);
        $this->assertTrue($players[0]->getMemberId()->equals($memberId));
        $this->assertTrue($players[0]->getGroupId()->equals($this->groupId));
        $this->assertEquals($username, $players[0]->getUsername());
    }
}
