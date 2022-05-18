<?php

namespace Tailgate\Test\Domain\Model;

use Burger\Aggregate\AggregateHistory;
use RuntimeException;
use Tailgate\Domain\Model\Common\Date;
use Tailgate\Domain\Model\Group\Follow;
use Tailgate\Domain\Model\Group\FollowId;
use Tailgate\Domain\Model\Group\Group;
use Tailgate\Domain\Model\Group\GroupId;
use Tailgate\Domain\Model\Group\GroupInviteCode;
use Tailgate\Domain\Model\Group\GroupRole;
use Tailgate\Domain\Model\Group\Member;
use Tailgate\Domain\Model\Group\MemberId;
use Tailgate\Domain\Model\Group\Player;
use Tailgate\Domain\Model\Group\PlayerId;
use Tailgate\Domain\Model\Group\Score;
use Tailgate\Domain\Model\Group\ScoreId;
use Tailgate\Domain\Model\Season\GameId;
use Tailgate\Domain\Model\Season\SeasonId;
use Tailgate\Domain\Model\Team\TeamId;
use Tailgate\Domain\Model\User\UserId;
use Tailgate\Test\BaseTestCase;

class GroupTest extends BaseTestCase
{
    private function createGroup()
    {
        return Group::create(
            $this->groupId,
            $this->groupName,
            $this->groupInviteCode,
            $this->ownerId,
            $this->dateOccurred
        );
    }

    public function setUp(): void
    {
        $this->groupId = GroupId::fromString('groupId');
        $this->groupName = 'groupName';
        $this->ownerId = UserId::fromString('ownerId');
        $this->groupInviteCode = GroupInviteCode::create();
        $this->dateOccurred = Date::fromDateTimeImmutable($this->getFakeTime()->currentTime());
    }

    public function testGroupShouldBeTheSameAfterReconstitution()
    {
        // create a group
        $group = $this->createGroup();
        $events = $group->getRecordedEvents();
        $group->clearRecordedEvents();

        // recreate the group using event array
        $reconstitutedGroup = Group::reconstituteFromEvents(
            new AggregateHistory($this->groupId, (array) $events)
        );

        // both group objects should be the same
        $this->assertEquals(
            $group,
            $reconstitutedGroup,
            'the reconstituted group does not match the original group'
        );
    }

    public function testAGroupCanBeCreated()
    {
        $group = $this->createGroup();

        $groupCreatedEvent = $group->getRecordedEvents()[0];

        $this->assertEquals($this->groupId, $groupCreatedEvent->getAggregateId());
        $this->assertEquals($this->groupName, $groupCreatedEvent->getName());
        $this->assertEquals($this->ownerId, $groupCreatedEvent->getOwnerId());
        $this->assertEquals($this->dateOccurred, $groupCreatedEvent->getDateOccurred());
        $this->assertEquals($this->groupId, $group->getGroupId());
        $this->assertEquals($this->groupName, $group->getName());
        $this->assertEquals($this->ownerId, $group->getOwnerId());
    }

    public function testAGroupHasNoScoresWhenCreated()
    {
        $group = $this->createGroup();

        $this->assertEmpty($group->getScores());
    }

    public function testUserWhoCreatesGroupIsAMemberOfGroupWhenGroupIsCreated()
    {
        $group = $this->createGroup();
        $members = $group->getMembers();

        $memberAddedEvent = $group->getRecordedEvents()[1];

        $this->assertTrue($memberAddedEvent->getMemberId() instanceof MemberId);
        $this->assertTrue($memberAddedEvent->getAggregateId()->equals($this->groupId));
        $this->assertTrue($memberAddedEvent->getUserId()->equals($this->ownerId));
        $this->assertEquals($this->dateOccurred, $memberAddedEvent->getDateOccurred());
        $this->assertCount(1, $members);
        $this->assertTrue($members[0] instanceof Member);
        $this->assertTrue($members[0]->getMemberId() instanceof MemberId);
        $this->assertTrue($members[0]->getGroupId()->equals($this->groupId));
        $this->assertTrue($members[0]->getUserId()->equals($this->ownerId));
    }

    public function testUserWhoCreatesGroupIsAGroupAdmin()
    {
        $group = $this->createGroup();
        $members = $group->getMembers();

        $memberAddedEvent = $group->getRecordedEvents()[1];

        $this->assertEquals($memberAddedEvent->getGroupRole(), GroupRole::getGroupAdmin());
        $this->assertEquals($members[0]->getGroupRole(), GroupRole::getGroupAdmin());
    }

    public function testUserWhoCreatesGroupCanAddMultiplePlayers()
    {
        $group = $this->createGroup();
        $members = $group->getMembers();

        $memberAddedEvent = $group->getRecordedEvents()[1];

        $this->assertEquals($memberAddedEvent->getAllowMultiplePlayers(), Group::MULTIPLE_PLAYERS);
        $this->assertEquals($members[0]->getAllowMultiplePlayers(), Group::MULTIPLE_PLAYERS);
    }

    public function testGroupHasAScoreWhenScoreIsSubmitted()
    {
        // create a group, add player
        $group = $this->createGroup();
        $memberId = $group->getMembers()[0]->getMemberId();
        $group->addPlayer($memberId, 'username', $this->dateOccurred);
        $playerId = $group->getPlayers()[0]->getPlayerId();
        $group->clearRecordedEvents();

        // player submits score
        $gameId = GameId::fromString('gameId');
        $homeTeamPrediction = '70';
        $awayTeamPrediction = '60';
        $group->submitScore($playerId, $gameId, $homeTeamPrediction, $awayTeamPrediction, $this->dateOccurred);
        $scores = $group->getScores();

        $scoreSubmittedEvent = $group->getRecordedEvents()[0];

        $this->assertTrue($scoreSubmittedEvent->getScoreId() instanceof ScoreId);
        $this->assertTrue($scoreSubmittedEvent->getAggregateId()->equals($this->groupId));
        $this->assertTrue($scoreSubmittedEvent->getPlayerId()->equals($playerId));
        $this->assertTrue($scoreSubmittedEvent->getGameId()->equals($gameId));
        $this->assertEquals($homeTeamPrediction, $scoreSubmittedEvent->getHomeTeamPrediction());
        $this->assertEquals($awayTeamPrediction, $scoreSubmittedEvent->getAwayTeamPrediction());
        $this->assertEquals($this->dateOccurred, $scoreSubmittedEvent->getDateOccurred());

        $this->assertCount(1, $scores);
        $this->assertTrue($scores[0] instanceof Score);
        $this->assertTrue($scores[0]->getScoreId() instanceof ScoreId);
        $this->assertTrue($scores[0]->getGroupId()->equals($this->groupId));
        $this->assertTrue($scores[0]->getPlayerId()->equals($playerId));
        $this->assertTrue($scores[0]->getGameId()->equals($gameId));
        $this->assertEquals($homeTeamPrediction, $scores[0]->getHomeTeamPrediction());
        $this->assertEquals($awayTeamPrediction, $scores[0]->getAwayTeamPrediction());
    }

    public function testGroupGetsAMemberWhenMemberIsAdded()
    {
        $group = $this->createGroup();
        $userId = UserId::fromString('userID');
        $group->clearRecordedEvents();

        $group->addMember($userId, $this->dateOccurred);
        $members = $group->getMembers();

        $memberAddedEvent = $group->getRecordedEvents()[0];

        $this->assertTrue($memberAddedEvent->getMemberId() instanceof MemberId);
        $this->assertTrue($memberAddedEvent->getAggregateId()->equals($this->groupId));
        $this->assertTrue($memberAddedEvent->getUserId()->equals($userId));
        $this->assertEquals($memberAddedEvent->getGroupRole(), GroupRole::getGroupMember());
        $this->assertEquals($memberAddedEvent->getAllowMultiplePlayers(), Group::SINGLE_PLAYER);
        $this->assertEquals($this->dateOccurred, $memberAddedEvent->getDateOccurred());

        // 2 because of the owner and new member
        $this->assertCount(2, $members);
        $this->assertTrue($members[1] instanceof Member);
        $this->assertTrue($members[1]->getMemberId() instanceof MemberId);
        $this->assertTrue($members[1]->getGroupId()->equals($this->groupId));
        $this->assertTrue($members[1]->getUserId()->equals($userId));
        $this->assertEquals($members[1]->getGroupRole(), GroupRole::getGroupMember());
        $this->assertEquals($members[1]->getAllowMultiplePlayers(), Group::SINGLE_PLAYER);
    }

    public function testExceptionThrownIfMemberAlreadyPartOfGroup()
    {
        $group = $this->createGroup();

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('The member is already in the group.');
        $group->addMember($this->ownerId, $this->dateOccurred);
    }

    public function testCannotAddMoreMembersThanLimit()
    {
        // one member added from group creation
        $group = $this->createGroup();

        // fill up rest of members
        for ($i = 1; $i < Group::MEMBER_LIMIT; $i++) {
            $group->addMember(UserId::fromString("userID{$i}"), $this->dateOccurred);
        }

        // exception should be thrown when adding one too many
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Group member limit reached.');
        $group->addMember(UserId::fromString("userShouldNotGoThrough"), $this->dateOccurred);
    }

    public function testThereAreNoMembersPlayersAndScoresWhenGroupIsDeleted()
    {
        // create group, add member, add player, submit score
        $group = $this->createGroup();
        $userId = UserId::fromString('userID');
        $group->addMember($userId, $this->dateOccurred);
        $memberId = $group->getMembers()[0]->getMemberId();
        $group->addPlayer($memberId, 'username', $this->dateOccurred);
        $playerId = $group->getPlayers()[0]->getPlayerId();
        $gameId = GameId::fromString('gameId');
        $homeTeamPrediction = '70';
        $awayTeamPrediction = '60';
        $group->submitScore($playerId, $gameId, $homeTeamPrediction, $awayTeamPrediction, $this->dateOccurred);
        $group->clearRecordedEvents();

        $group->delete($this->dateOccurred);

        $groupDeletedEvent = $group->getRecordedEvents()[0];

        $this->assertEquals($this->groupId, $groupDeletedEvent->getAggregateId());
        $this->assertEquals($this->dateOccurred, $groupDeletedEvent->getDateOccurred());
        $this->assertEmpty($group->getMembers());
        $this->assertEmpty($group->getPlayers());
        $this->assertEmpty($group->getScores());
    }

    public function testAMemberTheirPlayersAndTheirScoresAreRemovedWhenAMemberIsDeleted()
    {
        // create a group
        $group = $this->createGroup();

        // add two more members
        $userId2 = UserId::fromString('userId2');
        $userId3 = UserId::fromString('userId3');
        $group->addMember($userId2, $this->dateOccurred);
        $group->addMember($userId3, $this->dateOccurred);

        // ensure there are three
        $members = $group->getMembers();
        $this->assertCount(3, $members);

        // add one player to the first member
        $memberId1 = $group->getMembers()[0]->getMemberId();
        $group->addPlayer($memberId1, 'username1', $this->dateOccurred);
        // add two players to the third member
        $memberId3 = $group->getMembers()[2]->getMemberId();
        $group->updateMember($memberId3, GroupRole::getGroupAdmin(), Group::MULTIPLE_PLAYERS, $this->dateOccurred);
        $group->addPlayer($memberId3, 'username31', $this->dateOccurred);
        $group->addPlayer($memberId3, 'username32', $this->dateOccurred);
        $playerId1 = $group->getPlayers()[0]->getPlayerId();
        $playerId31 = $group->getPlayers()[1]->getPlayerId();
        $playerId32 = $group->getPlayers()[2]->getPlayerId();

        // add three scores
        $group->submitScore($playerId1, GameId::fromString('gameId'), 700, 600, $this->dateOccurred);
        $group->submitScore($playerId31, GameId::fromString('gameId'), 70, 60, $this->dateOccurred);
        $group->submitScore($playerId32, GameId::fromString('gameId'), 12, 34, $this->dateOccurred);

        $memberId1 = $members[0]->getMemberId();
        $memberId2 = $members[1]->getMemberId();
        $memberId3 = $members[2]->getMemberId();

        $members = $group->getMembers();
        $players = $group->getPlayers();
        $scores = $group->getScores();

        $this->assertCount(3, $members);
        $this->assertCount(3, $players);
        $this->assertCount(3, $scores);

        $group->deleteMember($memberId3, $this->dateOccurred);

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
        $group = $this->createGroup();

        // add two more members
        $userId2 = UserId::fromString('userId2');
        $userId3 = UserId::fromString('userId3');
        $group->addMember($userId2, $this->dateOccurred);
        $group->addMember($userId3, $this->dateOccurred);

        $members = $group->getMembers();
        $memberId1 = $members[0]->getMemberId();
        $memberId2 = $members[1]->getMemberId();
        $memberId3 = $members[2]->getMemberId();

        // update second member to admin
        $group->updateMember($memberId2, GroupRole::getGroupAdmin(), Group::SINGLE_PLAYER, $this->dateOccurred);

        // delete the member we just made an admin
        $group->deleteMember($memberId2, $this->dateOccurred);

        // try to delete the first member which is the last admin
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Cannot remove the last admin in a group.');
        $group->deleteMember($memberId1, $this->dateOccurred);
    }

    public function testAPlayersAndTheirScoresAreRemovedWhenAPlayerIsDeleted()
    {
        // create a group
        $group = $this->createGroup();

        // add two more members
        $userId2 = UserId::fromString('userId2');
        $userId3 = UserId::fromString('userId3');
        $group->addMember($userId2, $this->dateOccurred);
        $group->addMember($userId3, $this->dateOccurred);

        // add one player to the first member
        $memberId1 = $group->getMembers()[0]->getMemberId();
        $group->addPlayer($memberId1, 'username1', $this->dateOccurred);
        // add two players to the third member
        $memberId3 = $group->getMembers()[2]->getMemberId();
        $group->updateMember($memberId3, GroupRole::getGroupAdmin(), Group::MULTIPLE_PLAYERS, $this->dateOccurred);
        $group->addPlayer($memberId3, 'username31', $this->dateOccurred);
        $group->addPlayer($memberId3, 'username32', $this->dateOccurred);
        $playerId1 = $group->getPlayers()[0]->getPlayerId();
        $playerId31 = $group->getPlayers()[1]->getPlayerId();
        $playerId32 = $group->getPlayers()[2]->getPlayerId();

        $members = $group->getMembers();
        $players = $group->getPlayers();

        $this->assertCount(3, $members);
        $this->assertCount(3, $players);

        $group->deletePlayer($playerId1, $this->dateOccurred);

        $members = $group->getMembers();
        $players = $group->getPlayers();

        $this->assertCount(3, $members);
        $this->assertCount(2, $players);
        $this->assertTrue($players[0]->getPlayerId()->equals($playerId31));
        $this->assertTrue($players[1]->getPlayerId()->equals($playerId32));
    }

    public function testAPlayerMemberIsChangedToOtherMemberWhenAPlayerOwnerIsChanged()
    {
        // create group, add memeber, add player
        $group = $this->createGroup();
        $userId2 = UserId::fromString('userId2');
        $group->addMember($userId2, $this->dateOccurred);
        $memberId1 = $group->getMembers()[0]->getMemberId();
        $group->addPlayer($memberId1, 'username1', $this->dateOccurred);
        $playerId = (string) $group->getPlayers()[0]->getPlayerId();
        $players = $group->getPlayers();
        $this->assertEquals($this->groupId, $players[0]->getGroupId());
        $this->assertEquals($playerId, $players[0]->getPlayerId());
        $this->assertEquals($memberId1, $players[0]->getMemberId());
        $group->clearRecordedEvents();

        $memberId2 = (string) $group->getMembers()[1]->getMemberId();
        $playerId = (string) $group->getPlayers()[0]->getPlayerId();
        $group->changePlayerOwner(PlayerId::fromString($playerId), MemberId::fromString($memberId2), $this->dateOccurred);

        $playerOwnerChangedEvent = $group->getRecordedEvents()[0];
        $players = $group->getPlayers();
        $this->assertEquals($this->groupId, $playerOwnerChangedEvent->getAggregateId());
        $this->assertEquals($playerId, $playerOwnerChangedEvent->getPlayerId());
        $this->assertEquals($memberId2, $playerOwnerChangedEvent->getMemberId());
        $this->assertEquals($this->dateOccurred, $playerOwnerChangedEvent->getDateOccurred());
        $this->assertEquals($this->groupId, $players[0]->getGroupId());
        $this->assertEquals($playerId, $players[0]->getPlayerId());
        $this->assertEquals($memberId2, $players[0]->getMemberId());
    }

    public function testAScoreIsRemovedWhenAScoreIsDeleted()
    {
        // create group, add player, add score
        $group = $this->createGroup();
        $gameId = GameId::fromString('gameId');
        $homeTeamPrediction = '70';
        $awayTeamPrediction = '60';
        $memberId = $group->getMembers()[0]->getMemberId();
        $group->addPlayer($memberId, 'username', $this->dateOccurred);
        $playerId = $group->getPlayers()[0]->getPlayerId();
        $group->submitScore($playerId, $gameId, $homeTeamPrediction, $awayTeamPrediction, $this->dateOccurred);
        $group->clearRecordedEvents();
        $scores = $group->getScores();
        $this->assertCount(1, $scores);

        $scoreId = $scores[0]->getScoreId();
        $group->deleteScore($scoreId, $this->dateOccurred);

        $scoreDeletedEvent = $group->getRecordedEvents()[0];
        $scores = $group->getScores();

        $this->assertEquals($this->groupId, $scoreDeletedEvent->getAggregateId());
        $this->assertEquals($scoreId, $scoreDeletedEvent->getScoreId());
        $this->assertEquals($this->dateOccurred, $scoreDeletedEvent->getDateOccurred());
        $this->assertCount(0, $scores);
    }

    public function testAGroupCanBeUpdated()
    {
        $group = $this->createGroup();
        $group->clearRecordedEvents();

        $groupName = 'updatedgroupName';
        $ownerId = UserId::fromString('updatedownerId');
        $group->update($groupName, $ownerId, $this->dateOccurred);

        $groupUpdatedEvent = $group->getRecordedEvents()[0];

        $this->assertEquals($this->groupId, $groupUpdatedEvent->getAggregateId());
        $this->assertEquals($groupName, $groupUpdatedEvent->getName());
        $this->assertEquals($ownerId, $groupUpdatedEvent->getOwnerId());
        $this->assertEquals($this->dateOccurred, $groupUpdatedEvent->getDateOccurred());
        $this->assertEquals($groupName, $group->getName());
        $this->assertEquals($ownerId, $group->getOwnerId());
        $this->assertCount(1, $group->getMembers());
        $this->assertEmpty($group->getScores());
    }

    public function testAMemberCanBeUpdated()
    {
        // create group, add mmeber
        $group = $this->createGroup();
        $group->addMember(UserId::fromString('userId'), $this->dateOccurred);
        $group->clearRecordedEvents();

        $groupRole = GroupRole::getGroupMember();
        $allowMultiplePlayers = 'updatedAllowMultiplePlayers';
        $memberId = $group->getMembers()[1]->getMemberId();
        $group->updateMember($memberId, $groupRole, $allowMultiplePlayers, $this->dateOccurred);

        $memberUpdatedEvent = $group->getRecordedEvents()[0];
        $members = $group->getMembers();

        $this->assertEquals($this->groupId, $memberUpdatedEvent->getAggregateId());
        $this->assertEquals($groupRole, $memberUpdatedEvent->getGroupRole());
        $this->assertEquals($allowMultiplePlayers, $memberUpdatedEvent->getAllowMultiplePlayers());
        $this->assertEquals($this->dateOccurred, $memberUpdatedEvent->getDateOccurred());
        $this->assertEquals($this->groupId, $members[1]->getGroupId());
        $this->assertEquals($groupRole, $members[1]->getGroupRole());
        $this->assertEquals($allowMultiplePlayers, $members[1]->getAllowMultiplePlayers());
    }

    public function testCannotUpdateAnAdminToMemberStatusIfTheyAreTheLastAdminInTheGroup()
    {
        // create a group
        // owner is admin by default
        $group = $this->createGroup();
        $memberId = $group->getMembers()[0]->getMemberId();

        // try to update admin to member
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Cannot change the last admin in the group to not be an admin.');
        $group->updateMember($memberId, GroupRole::getGroupMember(), Group::SINGLE_PLAYER, $this->dateOccurred);
    }

    public function testAScoreCanBeUpdated()
    {
        // create group, add player, add score
        $group = $this->createGroup();
        $gameId = GameId::fromString('gameId');
        $memberId = $group->getMembers()[0]->getMemberId();
        $group->addPlayer($memberId, 'username', $this->dateOccurred);
        $playerId = $group->getPlayers()[0]->getPlayerId();
        $group->submitScore($playerId, $gameId, 1, 2, $this->dateOccurred);
        $scores = $group->getScores();
        $scoreId = $scores[0]->getScoreId();
        $group->clearRecordedEvents();

        $homeTeamPrediction = '700';
        $awayTeamPrediction = '600';
        $group->updateScore($scoreId, $homeTeamPrediction, $awayTeamPrediction, $this->dateOccurred);

        $groupScoreUpdatedEvent = $group->getRecordedEvents()[0];
        $scores = $group->getScores();

        $this->assertEquals($this->groupId, $groupScoreUpdatedEvent->getAggregateId());
        $this->assertEquals($homeTeamPrediction, $groupScoreUpdatedEvent->getHomeTeamPrediction());
        $this->assertEquals($awayTeamPrediction, $groupScoreUpdatedEvent->getAwayTeamPrediction());
        $this->assertEquals($this->dateOccurred, $groupScoreUpdatedEvent->getDateOccurred());
        $this->assertEquals($homeTeamPrediction, $scores[0]->getHomeTeamPrediction());
        $this->assertEquals($awayTeamPrediction, $scores[0]->getAwayTeamPrediction());
    }

    public function testGroupGetsAPlayerWhenPlayerIsAdded()
    {
        $group = $this->createGroup();
        $members = $group->getMembers();
        $players = $group->getPlayers();
        $this->assertCount(0, $players);
        $group->clearRecordedEvents();

        $memberId = $members[0]->getMemberId();
        $username = 'username';
        $group->addPlayer($memberId, $username, $this->dateOccurred);

        $playerAddedEvent = $group->getRecordedEvents()[0];
        $players = $group->getPlayers();

        $this->assertTrue($playerAddedEvent->getMemberId()->equals($memberId));
        $this->assertTrue($playerAddedEvent->getAggregateId()->equals($this->groupId));
        $this->assertEquals($username, $playerAddedEvent->getUsername());
        $this->assertEquals($this->dateOccurred, $playerAddedEvent->getDateOccurred());

        $this->assertCount(1, $players);
        $this->assertTrue($players[0] instanceof Player);
        $this->assertTrue($players[0]->getMemberId()->equals($memberId));
        $this->assertTrue($players[0]->getGroupId()->equals($this->groupId));
        $this->assertEquals($username, $players[0]->getUsername());
    }

    public function testCannotAddAPlayerForMemberPastPlayerLimitForSinglePlayerMember()
    {
        $group = $this->createGroup();
        $memberId = $group->getMembers()[0]->getMemberId();

        // change admin user to a singleplayer
        $group->updateMember($memberId, GroupRole::getGroupAdmin(), Group::SINGLE_PLAYER, $this->dateOccurred);

        // add one player
        $group->addPlayer($memberId, "playername", $this->dateOccurred);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Player limit reached for member.');
        $group->addPlayer($memberId, 'playerShouldNotGoThrough', $this->dateOccurred);
    }

    public function testCannotAddAPlayerForMemberPastPlayerLimitForMultiplePlayerMember()
    {
        $group = $this->createGroup();
        $memberId = $group->getMembers()[0]->getMemberId();

        // add players to reach limit
        for ($i = 0; $i < Group::PLAYER_LIMIT; $i++) {
            $group->addPlayer($memberId, "username{$i}", $this->dateOccurred);
        }

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Player limit reached for member.');
        $group->addPlayer($memberId, 'playerShouldNotGoThrough', $this->dateOccurred);
    }

    public function testAnExceptionThrownForPlayerThatDoesntExistWhenSubmittingScore()
    {
        $group = $this->createGroup();
        $memberId = $group->getMembers()[0]->getMemberId();
        $group->addPlayer($memberId, 'username', $this->dateOccurred);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('The player submitting the score does not exist.');
        $group->submitScore(PlayerId::fromString('playerIdThatDoesNotExist'), GameId::fromString('gameId'), 1, 2, $this->dateOccurred);
    }

    public function testAnExceptionThrownForPlayerThatHasAlreadySubmitAScoreForGame()
    {
        $group = $this->createGroup();
        $gameId = GameId::fromString('gameId');
        $memberId = $group->getMembers()[0]->getMemberId();
        $group->addPlayer($memberId, 'username', $this->dateOccurred);
        $playerId = $group->getPlayers()[0]->getPlayerId();
        $group->submitScore($playerId, $gameId, 1, 2, $this->dateOccurred);


        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('The player already submitted a score for this game.');
        $group->submitScore($playerId, $gameId, 1, 2, $this->dateOccurred);
    }

    public function testAnExceptionThrownForMemberThatDoesntExistWhenAddingPlayer()
    {
        $group = $this->createGroup();

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('The member does not exist. Cannot add the player.');
        $group->addPlayer(MemberId::fromString('memberIdThatDoesNotExist'), 'username', $this->dateOccurred);
    }

    public function testAnExceptionThrownForScoreThatDoesntExistWhenUpdatingScore()
    {
        $group = $this->createGroup();
        $gameId = GameId::fromString('gameId');
        $memberId = $group->getMembers()[0]->getMemberId();
        $group->addPlayer($memberId, 'username', $this->dateOccurred);
        $playerId = $group->getPlayers()[0]->getPlayerId();
        $group->submitScore($playerId, $gameId, 1, 2, $this->dateOccurred);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('The score does not exist. Cannot update the score.');
        $group->updateScore(ScoreId::fromString('scoreIdThatDoesNotExist'), 1, 2, $this->dateOccurred);
    }

    public function testUpdateMemberThrowsExceptionsWithInvalidValues()
    {
        $group = $this->createGroup();
        $members = $group->getMembers();
        $memberId = $group->getMembers()[0]->getMemberId();

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('The member does not exist.');
        $group->updateMember(MemberID::fromString('invalidMemberId'), GroupRole::getGroupAdmin(), 1, $this->dateOccurred);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('The score does not exist. Cannot update the score.');
        $group->updateMember($memberId, 'invalidGroupRole', 1, $this->dateOccurred);
    }

    public function testDeleteMemberThrowsExceptionsWithInvalidValues()
    {
        $group = $this->createGroup();
        $userId2 = UserId::fromString('userId2');
        $userId3 = UserId::fromString('userId3');
        $group->addMember($userId2, $this->dateOccurred);
        $group->addMember($userId3, $this->dateOccurred);
        $members = $group->getMembers();

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('The member does not exist.');
        $group->deleteMember(MemberID::fromString('invalidMemberId'), $this->dateOccurred);
    }

    public function testDeleteScoreThrowsExceptionsWithInvalidValues()
    {
        $group = $this->createGroup();
        $gameId = GameId::fromString('gameId');
        $memberId = $group->getMembers()[0]->getMemberId();
        $group->addPlayer($memberId, 'username', $this->dateOccurred);
        $playerId = $group->getPlayers()[0]->getPlayerId();
        $group->submitScore($playerId, $gameId, 1, 2, $this->dateOccurred);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('The score does not exist.');
        $group->deleteScore(ScoreId::fromString('scoreIdThatDoesNotExist'), $this->dateOccurred);
    }

    public function testFollowAddedWhenTeamIsFollowed()
    {
        $group = $this->createGroup();
        $group->clearRecordedEvents();

        $this->assertNull($group->getFollow());
        $teamId = TeamId::fromString('teamId');
        $seasonId = SeasonId::fromString('seasonId');
        $group->followTeam($teamId, $seasonId, $this->dateOccurred);

        $teamFollowedEvent = $group->getRecordedEvents()[0];
        $follow = $group->getFollow();

        $this->assertTrue($teamFollowedEvent->getFollowId() instanceof FollowId);
        $this->assertTrue($teamFollowedEvent->getAggregateId()->equals($this->groupId));
        $this->assertTrue($teamFollowedEvent->getSeasonId()->equals($seasonId));
        $this->assertTrue($teamFollowedEvent->getTeamId()->equals($teamId));
        $this->assertEquals($this->dateOccurred, $teamFollowedEvent->getDateOccurred());

        $this->assertTrue($follow instanceof Follow);
        $this->assertTrue($follow->getFollowId() instanceof FollowId);
        $this->assertTrue($follow->getGroupId()->equals($this->groupId));
        $this->assertTrue($follow->getSeasonId()->equals($seasonId));
        $this->assertTrue($follow->getTeamId()->equals($teamId));
    }

    public function testExceptionThrownWhenTeamIsAlreadyFollowedByGroup()
    {
        $group = $this->createGroup();
        $teamId = TeamId::fromString('teamId');
        $seasonId = SeasonId::fromString('seasonId');

        $group->followTeam($teamId, $seasonId, $this->dateOccurred);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Cannot follow this team. This group is already following a team.');
        $group->followTeam($teamId, $seasonId, $this->dateOccurred);
    }

    public function testAFollowCanBeDeleted()
    {
        // create a group, follow a team
        $group = $this->createGroup();
        $this->assertNull($group->getFollow());
        $teamId = TeamId::fromString('teamId');
        $seasonId = SeasonId::fromString('seasonId');
        $group->followTeam($teamId, $seasonId, $this->dateOccurred);
        $follow = $group->getFollow();
        $group->clearRecordedEvents();

        // remove follow
        $group->deleteFollow($follow->getFollowId(), $this->dateOccurred);

        $followDeletedEvent = $group->getRecordedEvents()[0];

        $this->assertEquals($this->groupId, $followDeletedEvent->getAggregateId());
        $this->assertEquals($follow->getFollowId(), $followDeletedEvent->getFollowId());
        $this->assertEquals($this->dateOccurred, $followDeletedEvent->getDateOccurred());
        $this->assertNull($group->getFollow());
    }

    public function testExceptionThrownWhenDeletingFollowThatDoesNotExist()
    {
        $group = $this->createGroup();

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Group is not following a team.');
        $group->deleteFollow(FollowId::fromString('followThatDoesNotExist'), $this->dateOccurred);
    }
}
