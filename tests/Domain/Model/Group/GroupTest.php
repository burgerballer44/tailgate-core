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
use Tailgate\Domain\Model\ModelException;
use Tailgate\Domain\Model\Season\GameId;
use Tailgate\Domain\Model\User\UserId;

class GroupTest extends TestCase
{
    private $groupId;
    private $groupName;
    private $groupInviteCode;
    private $ownerId;

    public function setUp()
    {
        $this->groupId = GroupId::fromString('groupId');
        $this->groupName = 'groupName';
        $this->ownerId = UserId::fromString('ownerId');
    }

    public function testGroupShouldBeTheSameAfterReconstitution()
    {
        $group = Group::create($this->groupId, $this->groupName, $this->groupInviteCode, $this->ownerId);
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
        $group = Group::create($this->groupId, $this->groupName, $this->groupInviteCode, $this->ownerId);

        $this->assertEquals($this->groupId, $group->getId());
        $this->assertEquals($this->groupName, $group->getName());
        $this->assertEquals($this->ownerId, $group->getOwnerId());
        $this->assertCount(1, $group->getMembers());
        $this->assertEmpty($group->getScores());
    }

    public function testUserWhoCreatesGroupIsAMemberOfGroupWhenGroupIsCreated()
    {
        $group = Group::create($this->groupId, $this->groupName, $this->groupInviteCode, $this->ownerId);
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
        $group = Group::create($this->groupId, $this->groupName, $this->groupInviteCode, $this->ownerId);
        $userId = UserId::fromString('userID');
        $gameId = GameId::fromString('gameId');
        $homeTeamPrediction = '70';
        $awayTeamPrediction = '60';

        $memberId = $group->getMembers()[0]->getMemberId();
        $group->addPlayer($memberId, 'username');
        $playerId = $group->getPlayers()[0]->getPlayerId();

        $group->submitScore($playerId, $gameId, $homeTeamPrediction, $awayTeamPrediction);
        $scores = $group->getScores();

        $this->assertCount(1, $scores);
        $this->assertTrue($scores[0] instanceof Score);
        $this->assertTrue($scores[0]->getScoreId() instanceof ScoreId);
        $this->assertTrue($scores[0]->getGroupId()->equals($this->groupId));
        $this->assertTrue($scores[0]->getPlayerId()->equals($playerId));
        $this->assertTrue($scores[0]->getMemberId()->equals($memberId));
        $this->assertTrue($scores[0]->getGameId()->equals($gameId));
        $this->assertEquals($homeTeamPrediction, $scores[0]->getHomeTeamPrediction());
        $this->assertEquals($awayTeamPrediction, $scores[0]->getAwayTeamPrediction());
    }

    public function testGroupGetsAMemberWhenMemberIsAdded()
    {
        $group = Group::create($this->groupId, $this->groupName, $this->groupInviteCode, $this->ownerId);
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

    public function testExceptionThrownIfMemberAlreadyPartOfGroup()
    {
        $group = Group::create($this->groupId, $this->groupName, $this->groupInviteCode, $this->ownerId);

        $this->expectException(ModelException::class);
        $this->expectExceptionMessage('The member is already in the group.');
        $group->addMember($this->ownerId);
    }

    public function testCannotAddMoreMembersThanLimit()
    {
        // one member added from group creation
        $group = Group::create($this->groupId, $this->groupName, $this->groupInviteCode, $this->ownerId);

        // fill up rest of members
        for ($i = 1; $i < Group::MEMBER_LIMIT; $i++) {
            $group->addMember(UserId::fromString("userID{$i}"));
        }

        $this->expectException(ModelException::class);
        $this->expectExceptionMessage('Group member limit reached.');
        $group->addMember(UserId::fromString("userShouldNotGoThrough"));
    }

    public function testThereAreNoMembersAndScoresWhenGroupIsDeleted()
    {
        $group = Group::create($this->groupId, $this->groupName, $this->groupInviteCode, $this->ownerId);
        $userId = UserId::fromString('userID');
        $gameId = GameId::fromString('gameId');
        $homeTeamPrediction = '70';
        $awayTeamPrediction = '60';
        $group->addMember($userId);

        $memberId = $group->getMembers()[0]->getMemberId();
        $group->addPlayer($memberId, 'username');
        $playerId = $group->getPlayers()[0]->getPlayerId();

        $group->submitScore($playerId, $gameId, $homeTeamPrediction, $awayTeamPrediction);

        $group->delete();

        $members = $group->getMembers();
        $players = $group->getPlayers();
        $scores = $group->getScores();

        $this->assertCount(0, $members);
        $this->assertCount(0, $players);
        $this->assertCount(0, $scores);
    }

    public function testAMemberTheirPlayersAndTheirScoresAreRemovedWhenAMemberIsDeleted()
    {
        // create a group
        $group = Group::create($this->groupId, $this->groupName, $this->groupInviteCode, $this->ownerId);

        // add two more members
        $userId2 = UserId::fromString('userId2');
        $userId3 = UserId::fromString('userId3');
        $group->addMember($userId2);
        $group->addMember($userId3);

        // ensure there are three
        $members = $group->getMembers();
        $this->assertCount(3, $members);

        // add one player to the first member
        $memberId1 = $group->getMembers()[0]->getMemberId();
        $group->addPlayer($memberId1, 'username1');
        // add two players to the third member
        $memberId3 = $group->getMembers()[2]->getMemberId();
        $group->addPlayer($memberId3, 'username31');
        $group->addPlayer($memberId3, 'username32');
        $playerId1 = $group->getPlayers()[0]->getPlayerId();
        $playerId31 = $group->getPlayers()[1]->getPlayerId();
        $playerId32 = $group->getPlayers()[2]->getPlayerId();

        // add three scores
        $group->submitScore($playerId1, GameId::fromString('gameId'), 700, 600);
        $group->submitScore($playerId31, GameId::fromString('gameId'), 70, 60);
        $group->submitScore($playerId32, GameId::fromString('gameId'), 12, 34);

        $memberId1 = $members[0]->getMemberId();
        $memberId2 = $members[1]->getMemberId();
        $memberId3 = $members[2]->getMemberId();

        $members = $group->getMembers();
        $players = $group->getPlayers();
        $scores = $group->getScores();

        $this->assertCount(3, $members);
        $this->assertCount(3, $players);
        $this->assertCount(3, $scores);

        $group->deleteMember($memberId3);

        $members = $group->getMembers();
        $players = $group->getPlayers();
        $scores = $group->getScores();

        $this->assertCount(2, $members);
        $this->assertCount(1, $players);
        $this->assertCount(1, $scores);
        $this->assertTrue($members[0]->getMemberId()->equals($memberId1));
        $this->assertTrue($members[1]->getMemberId()->equals($memberId2));
    }

    public function testCannotDeleteAnAdminIfTheyAreTheLastAdminInTheGroup()
    {
        // create a group
        // owner is admin by default
        $group = Group::create($this->groupId, $this->groupName, $this->groupInviteCode, $this->ownerId);

        // add two more members
        $userId2 = UserId::fromString('userId2');
        $userId3 = UserId::fromString('userId3');
        $group->addMember($userId2);
        $group->addMember($userId3);

        $members = $group->getMembers();
        $memberId1 = $members[0]->getMemberId();
        $memberId2 = $members[1]->getMemberId();
        $memberId3 = $members[2]->getMemberId();

        // update second member to admin
        $group->updateMember($memberId2, Group::G_ROLE_ADMIN, Group::SINGLE_PLAYER);

        // delete the member we just made an admin
        $group->deleteMember($memberId2);

        // try to delete the first member which is the last admin
        $this->expectException(ModelException::class);
        $this->expectExceptionMessage('Cannot remove the last admin in a group.');
        $group->deleteMember($memberId1);
    }


    public function testAPlayersAndTheirScoresAreRemovedWhenAPlayerIsDeleted()
    {
        // create a group
        $group = Group::create($this->groupId, $this->groupName, $this->groupInviteCode, $this->ownerId);

        // add two more members
        $userId2 = UserId::fromString('userId2');
        $userId3 = UserId::fromString('userId3');
        $group->addMember($userId2);
        $group->addMember($userId3);

        // add one player to the first member
        $memberId1 = $group->getMembers()[0]->getMemberId();
        $group->addPlayer($memberId1, 'username1');
        // add two players to the third member
        $memberId3 = $group->getMembers()[2]->getMemberId();
        $group->addPlayer($memberId3, 'username31');
        $group->addPlayer($memberId3, 'username32');
        $playerId1 = $group->getPlayers()[0]->getPlayerId();
        $playerId31 = $group->getPlayers()[1]->getPlayerId();
        $playerId32 = $group->getPlayers()[2]->getPlayerId();

        $members = $group->getMembers();
        $players = $group->getPlayers();

        $this->assertCount(3, $members);
        $this->assertCount(3, $players);

        $group->deletePlayer($playerId1);

        $members = $group->getMembers();
        $players = $group->getPlayers();

        $this->assertCount(3, $members);
        $this->assertCount(2, $players);
        $this->assertTrue($players[0]->getPlayerId()->equals($playerId31));
        $this->assertTrue($players[1]->getPlayerId()->equals($playerId32));
    }

    public function testAScoreIsRemovedWhenAScoreIsDeleted()
    {
        $group = Group::create($this->groupId, $this->groupName, $this->groupInviteCode, $this->ownerId);
        $gameId = GameId::fromString('gameId');
        $homeTeamPrediction = '70';
        $awayTeamPrediction = '60';

        $memberId = $group->getMembers()[0]->getMemberId();
        $group->addPlayer($memberId, 'username');
        $playerId = $group->getPlayers()[0]->getPlayerId();

        $group->submitScore($playerId, $gameId, $homeTeamPrediction, $awayTeamPrediction);
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

        $group = Group::create($this->groupId, $this->groupName, $this->groupInviteCode, $this->ownerId);

        $group->update($groupName, $ownerId);

        $this->assertEquals($this->groupId, $group->getId());
        $this->assertEquals($groupName, $group->getName());
        $this->assertEquals($ownerId, $group->getOwnerId());
        $this->assertCount(1, $group->getMembers());
        $this->assertEmpty($group->getScores());
    }

    public function testAMemberCanBeUpdated()
    {
        $groupRole = Group::G_ROLE_MEMBER;
        $allowMultiplePlayers = 'updatedAllowMultiplePlayers';
        $group = Group::create($this->groupId, $this->groupName, $this->groupInviteCode, $this->ownerId);

        $group->addMember(UserId::fromString('userId'));

        $memberId = $group->getMembers()[1]->getMemberId();
        $group->updateMember($memberId, $groupRole, $allowMultiplePlayers);

        $members = $group->getMembers();
        $this->assertEquals($this->groupId, $members[1]->getGroupId());
        $this->assertEquals($groupRole, $members[1]->getGroupRole());
        $this->assertEquals($allowMultiplePlayers, $members[1]->getAllowMultiplePlayers());
    }

    public function testCannotUpdateAnAdminToMemberStatusIfTheyAreTheLastAdminInTheGroup()
    {
        // create a group
        // owner is admin by default
        $group = Group::create($this->groupId, $this->groupName, $this->groupInviteCode, $this->ownerId);
        $memberId = $group->getMembers()[0]->getMemberId();

        // try to update admin to member
        $this->expectException(ModelException::class);
        $this->expectExceptionMessage('Cannot change the last admin in the group to not be an admin.');
        $group->updateMember($memberId, Group::G_ROLE_MEMBER, Group::SINGLE_PLAYER);
    }

    public function testAScoreCanBeUpdated()
    {
        $homeTeamPrediction = '700';
        $awayTeamPrediction = '600';
        $group = Group::create($this->groupId, $this->groupName, $this->groupInviteCode, $this->ownerId);
        $gameId = GameId::fromString('gameId');

        $memberId = $group->getMembers()[0]->getMemberId();
        $group->addPlayer($memberId, 'username');
        $playerId = $group->getPlayers()[0]->getPlayerId();

        $group->submitScore($playerId, $gameId, 1, 2);
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
        $group = Group::create($this->groupId, $this->groupName, $this->groupInviteCode, $this->ownerId);
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

    public function testCannotAddAPlayerForMemberPastPlayerLimit()
    {
        $group = Group::create($this->groupId, $this->groupName, $this->groupInviteCode, $this->ownerId);
        $memberId = $group->getMembers()[0]->getMemberId();

        // add players to reach limit
        for ($i = 0; $i < Group::PLAYER_LIMIT; $i++) {
            $group->addPlayer($memberId, "username{$i}");
        }

        $this->expectException(ModelException::class);
        $this->expectExceptionMessage('Player limit reached for member.');
        $group->addPlayer($memberId, 'playerShouldNotGoThrough');
    }

    public function testAnExceptionThrownForPlayerThatDoesntExistWhenSubmittingScore()
    {
        $group = Group::create($this->groupId, $this->groupName, $this->groupInviteCode, $this->ownerId);
        $memberId = $group->getMembers()[0]->getMemberId();
        $group->addPlayer($memberId, 'username');

        $this->expectException(ModelException::class);
        $this->expectExceptionMessage('The player submitting the score does not exist.');
        $group->submitScore(PlayerId::fromString('playerIdThatDoesNotExist'), GameId::fromString('gameId'), 1, 2);
    }

    public function testAnExceptionThrownForPlayerThatHasAlreadySubmitAScoreForGame()
    {
        $group = Group::create($this->groupId, $this->groupName, $this->groupInviteCode, $this->ownerId);
        $gameId = GameId::fromString('gameId');
        $memberId = $group->getMembers()[0]->getMemberId();
        $group->addPlayer($memberId, 'username');
        $playerId = $group->getPlayers()[0]->getPlayerId();
        $group->submitScore($playerId, $gameId, 1, 2);


        $this->expectException(ModelException::class);
        $this->expectExceptionMessage('The player already submitted a score for this game.');
        $group->submitScore($playerId, $gameId, 1, 2);
    }

    public function testAnExceptionThrownForMemberThatDoesntExistWhenAddingPlayer()
    {
        $group = Group::create($this->groupId, $this->groupName, $this->groupInviteCode, $this->ownerId);

        $this->expectException(ModelException::class);
        $this->expectExceptionMessage('The member does not exist. Cannot add the player.');
        $group->addPlayer(MemberId::fromString('memberIdThatDoesNotExist'), 'username');
    }

    public function testAnExceptionThrownForScoreThatDoesntExistWhenUpdatingScore()
    {
        $group = Group::create($this->groupId, $this->groupName, $this->groupInviteCode, $this->ownerId);
        $gameId = GameId::fromString('gameId');
        $memberId = $group->getMembers()[0]->getMemberId();
        $group->addPlayer($memberId, 'username');
        $playerId = $group->getPlayers()[0]->getPlayerId();
        $group->submitScore($playerId, $gameId, 1, 2);

        $this->expectException(ModelException::class);
        $this->expectExceptionMessage('The score does not exist. Cannot update the score.');
        $group->updateScore(ScoreId::fromString('scoreIdThatDoesNotExist'), 1, 2);
    }

    public function testUpdateMemberThrowsExceptionsWithInvalidValues()
    {
        $group = Group::create($this->groupId, $this->groupName, $this->groupInviteCode, $this->ownerId);
        $members = $group->getMembers();
        $memberId = $group->getMembers()[0]->getMemberId();

        $this->expectException(ModelException::class);
        $this->expectExceptionMessage('The member does not exist.');
        $group->updateMember(MemberID::fromString('invalidMemberId'), Group::G_ROLE_ADMIN, 1);

        $this->expectException(ModelException::class);
        $this->expectExceptionMessage('The score does not exist. Cannot update the score.');
        $group->updateMember($memberId, 'invalidGroupRole', 1);
    }

    public function testDeleteMemberThrowsExceptionsWithInvalidValues()
    {
        $group = Group::create($this->groupId, $this->groupName, $this->groupInviteCode, $this->ownerId);
        $userId2 = UserId::fromString('userId2');
        $userId3 = UserId::fromString('userId3');
        $group->addMember($userId2);
        $group->addMember($userId3);
        $members = $group->getMembers();

        $this->expectException(ModelException::class);
        $this->expectExceptionMessage('The member does not exist.');
        $group->deleteMember(MemberID::fromString('invalidMemberId'));
    }

    public function testDeleteScoreThrowsExceptionsWithInvalidValues()
    {
        $group = Group::create($this->groupId, $this->groupName, $this->groupInviteCode, $this->ownerId);
        $gameId = GameId::fromString('gameId');
        $memberId = $group->getMembers()[0]->getMemberId();
        $group->addPlayer($memberId, 'username');
        $playerId = $group->getPlayers()[0]->getPlayerId();
        $group->submitScore($playerId, $gameId, 1, 2);

        $this->expectException(ModelException::class);
        $this->expectExceptionMessage('The score does not exist.');
        $group->deleteScore(ScoreId::fromString('scoreIdThatDoesNotExist'));
    }
}
