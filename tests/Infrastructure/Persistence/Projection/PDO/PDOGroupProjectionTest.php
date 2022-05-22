<?php

namespace Infrastructure\Persistence\Projection\PDO;

use Tailgate\Domain\Model\Common\Date;
use Tailgate\Domain\Model\Group\FollowDeleted;
use Tailgate\Domain\Model\Group\FollowId;
use Tailgate\Domain\Model\Group\GroupCreated;
use Tailgate\Domain\Model\Group\GroupDeleted;
use Tailgate\Domain\Model\Group\GroupId;
use Tailgate\Domain\Model\Group\GroupRole;
use Tailgate\Domain\Model\Group\GroupScoreUpdated;
use Tailgate\Domain\Model\Group\GroupUpdated;
use Tailgate\Domain\Model\Group\MemberAdded;
use Tailgate\Domain\Model\Group\MemberDeleted;
use Tailgate\Domain\Model\Group\MemberId;
use Tailgate\Domain\Model\Group\MemberUpdated;
use Tailgate\Domain\Model\Group\PlayerAdded;
use Tailgate\Domain\Model\Group\PlayerDeleted;
use Tailgate\Domain\Model\Group\PlayerId;
use Tailgate\Domain\Model\Group\PlayerOwnerChanged;
use Tailgate\Domain\Model\Group\ScoreDeleted;
use Tailgate\Domain\Model\Group\ScoreId;
use Tailgate\Domain\Model\Group\ScoreSubmitted;
use Tailgate\Domain\Model\Group\TeamFollowed;
use Tailgate\Domain\Model\Season\GameId;
use Tailgate\Domain\Model\Season\SeasonId;
use Tailgate\Domain\Model\Team\TeamId;
use Tailgate\Domain\Model\User\UserId;
use Tailgate\Infrastructure\Persistence\Projection\PDO\GroupProjection;
use Tailgate\Test\BaseTestCase;

class PDOGroupProjectionTest extends BaseTestCase
{
    private $pdoMock;
    private $pdoStatementMock;
    private $projection;

    public function setUp(): void
    {
        $this->pdoMock = $this->createMock(\PDO::class);
        $this->pdoStatementMock = $this->createMock(\PDOStatement::class);
        $this->projection = new GroupProjection($this->pdoMock);
    }

    public function testItCanProjectGroupCreated()
    {
        $event = new GroupCreated(
            GroupId::fromString('groupId'),
            'name',
            'code',
            UserId::fromString('userId'),
            Date::fromDateTimeImmutable($this->getFakeTime()->currentTime())
        );

        // the pdo mock should call prepare and return a pdostatement mock
        $this->pdoMock
            ->expects($this->once())
            ->method('prepare')
            ->with('INSERT INTO `group` (group_id, name, invite_code, owner_id, created_at)
            VALUES (:group_id, :name, :invite_code, :owner_id, :created_at)')
            ->willReturn($this->pdoStatementMock);

        // execute method called once
        $this->pdoStatementMock
            ->expects($this->once())
            ->method('execute')
            ->with([
                ':group_id' => $event->getAggregateId(),
                ':name' => $event->getName(),
                ':invite_code' => $event->getInviteCode(),
                ':owner_id' => $event->getOwnerId(),
                ':created_at' => $event->getDateOccurred(),
            ]);

        $this->projection->projectGroupCreated($event);
    }

    public function testItCanProjectMemberAdded()
    {
        $event = new MemberAdded(
            GroupId::fromString('groupId'),
            MemberId::fromString('memberId'),
            UserId::fromString('userId'),
            GroupRole::fromString('Group-Admin'),
            0,
            Date::fromDateTimeImmutable($this->getFakeTime()->currentTime())
        );

        // the pdo mock should call prepare and return a pdostatement mock
        $this->pdoMock
            ->expects($this->once())
            ->method('prepare')
            ->with('INSERT INTO `member` (member_id, group_id, user_id, role, allow_multiple, created_at)
            VALUES (:member_id, :group_id, :user_id, :role, :allow_multiple, :created_at)')
            ->willReturn($this->pdoStatementMock);

        // execute method called once
        $this->pdoStatementMock
            ->expects($this->once())
            ->method('execute')
            ->with([
                ':group_id' => $event->getAggregateId(),
                ':member_id' => $event->getMemberId(),
                ':user_id' => $event->getUserId(),
                ':role' => $event->getGroupRole(),
                ':allow_multiple' => $event->getAllowMultiplePlayers(),
                ':created_at' => $event->getDateOccurred(),
            ]);

        $this->projection->projectMemberAdded($event);
    }

    public function testItCanProjectMemberUpdated()
    {
        $event = new MemberUpdated(
            GroupId::fromString('groupId'),
            MemberId::fromString('memberId'),
            GroupRole::fromString('Group-Admin'),
            0,
            Date::fromDateTimeImmutable($this->getFakeTime()->currentTime())
        );

        // the pdo mock should call prepare and return a pdostatement mock
        $this->pdoMock
            ->expects($this->once())
            ->method('prepare')
            ->with('UPDATE `member` SET member_id = :member_id, role =:role, allow_multiple = :allow_multiple
            WHERE member_id = :member_id')
            ->willReturn($this->pdoStatementMock);

        // execute method called once
        $this->pdoStatementMock
            ->expects($this->once())
            ->method('execute')
            ->with([
                ':member_id' => $event->getMemberId(),
                ':role' => $event->getGroupRole(),
                ':allow_multiple' => $event->getAllowMultiplePlayers(),
            ]);

        $this->projection->projectMemberUpdated($event);
    }

    public function testItCanProjectScoreSubmitted()
    {
        $event = new ScoreSubmitted(
            GroupId::fromString('groupId'),
            ScoreId::fromString('scoreId'),
            PlayerId::fromString('playerId'),
            GameId::fromString('gameId'),
            80,
            70,
            Date::fromDateTimeImmutable($this->getFakeTime()->currentTime())
        );

        // the pdo mock should call prepare and return a pdostatement mock
        $this->pdoMock
            ->expects($this->once())
            ->method('prepare')
            ->with('INSERT INTO `score` (score_id, group_id, player_id, game_id, home_team_prediction, away_team_prediction, created_at)
            VALUES (:score_id, :group_id, :player_id, :game_id, :home_team_prediction, :away_team_prediction, :created_at)')
            ->willReturn($this->pdoStatementMock);

        // execute method called once
        $this->pdoStatementMock
            ->expects($this->once())
            ->method('execute')
            ->with([
                ':group_id' => $event->getAggregateId(),
                ':score_id' => $event->getScoreId(),
                ':player_id' => $event->getPlayerId(),
                ':game_id' => $event->getGameId(),
                ':home_team_prediction' => $event->getHomeTeamPrediction(),
                ':away_team_prediction' => $event->getAwayTeamPrediction(),
                ':created_at' => $event->getDateOccurred(),
            ]);

        $this->projection->projectScoreSubmitted($event);
    }

    public function testItCanProjectGroupUpdated()
    {
        $event = new GroupUpdated(
            GroupId::fromString('groupId'),
            'name',
            UserId::fromString('userId'),
            Date::fromDateTimeImmutable($this->getFakeTime()->currentTime())
        );

        // the pdo mock should call prepare and return a pdostatement mock
        $this->pdoMock
            ->expects($this->once())
            ->method('prepare')
            ->with('UPDATE `group` SET name = :name, owner_id = :owner_id
            WHERE group_id = :group_id')
            ->willReturn($this->pdoStatementMock);

        // execute method called once
        $this->pdoStatementMock
            ->expects($this->once())
            ->method('execute')
            ->with([
                ':group_id' => $event->getAggregateId(),
                ':name' => $event->getName(),
                ':owner_id' => $event->getOwnerId(),
            ]);

        $this->projection->projectGroupUpdated($event);
    }

    public function testItCanProjectPlayerOwnerChanged()
    {
        $event = new PlayerOwnerChanged(
            GroupId::fromString('groupId'),
            PlayerId::fromString('playerId'),
            MemberId::fromString('memberId'),
            Date::fromDateTimeImmutable($this->getFakeTime()->currentTime())
        );

        // the pdo mock should call prepare and return a pdostatement mock
        $this->pdoMock
            ->expects($this->once())
            ->method('prepare')
            ->with('UPDATE `player` SET member_id = :member_id WHERE player_id = :player_id')
            ->willReturn($this->pdoStatementMock);

        // execute method called once
        $this->pdoStatementMock
            ->expects($this->once())
            ->method('execute')
            ->with([
                ':player_id' => $event->getPlayerId(),
                ':member_id' => $event->getMemberId(),
            ]);

        $this->projection->projectPlayerOwnerChanged($event);
    }

    public function testItCanProjectMemberDeleted()
    {
        $event = new MemberDeleted(
            GroupId::fromString('groupId'),
            MemberId::fromString('memberId'),
            Date::fromDateTimeImmutable($this->getFakeTime()->currentTime())
        );

        // the pdo mock should call prepare and return a pdostatement mock
        $this->pdoMock
            ->expects($this->exactly(3))
            ->method('prepare')
            ->withConsecutive(
                ['DELETE FROM `score` WHERE member_id = :member_id'],
                ['DELETE FROM `player` WHERE member_id = :member_id'],
                ['DELETE FROM `member` WHERE member_id = :member_id']
            )
            ->willReturn($this->pdoStatementMock);

        // execute method called thee times
        $this->pdoStatementMock
            ->expects($this->exactly(3))
            ->method('execute')
            ->with([':member_id' => $event->getMemberId()]);

        $this->projection->projectMemberDeleted($event);
    }

    public function testItCanProjectPlayerDeleted()
    {
        $event = new PlayerDeleted(
            GroupId::fromString('groupId'),
            PlayerId::fromString('playerId'),
            Date::fromDateTimeImmutable($this->getFakeTime()->currentTime())
        );

        // the pdo mock should call prepare and return a pdostatement mock
        $this->pdoMock
            ->expects($this->exactly(2))
            ->method('prepare')
            ->withConsecutive(
                ['DELETE FROM `score` WHERE player_id = :player_id'],
                ['DELETE FROM `player` WHERE player_id = :player_id']
            )
            ->willReturn($this->pdoStatementMock);

        // execute method called once
        $this->pdoStatementMock
            ->expects($this->exactly(2))
            ->method('execute')
            ->with([':player_id' => $event->getPlayerId()]);

        $this->projection->projectPlayerDeleted($event);
    }

    public function testItCanProjectScoreDeleted()
    {
        $event = new ScoreDeleted(
            GroupId::fromString('groupId'),
            ScoreId::fromString('scoreId'),
            Date::fromDateTimeImmutable($this->getFakeTime()->currentTime())
        );

        // the pdo mock should call prepare and return a pdostatement mock
        $this->pdoMock
            ->expects($this->once())
            ->method('prepare')
            ->with('DELETE FROM `score` WHERE score_id = :score_id')
            ->willReturn($this->pdoStatementMock);

        // execute method called once
        $this->pdoStatementMock
            ->expects($this->once())
            ->method('execute')
            ->with([':score_id' => $event->getScoreId()]);

        $this->projection->projectScoreDeleted($event);
    }

    public function testItCanProjectGroupDeleted()
    {
        $event = new GroupDeleted(GroupId::fromString('groupId'), Date::fromDateTimeImmutable($this->getFakeTime()->currentTime()));

        // the pdo mock should call prepare and return a pdostatement mock
        $this->pdoMock
            ->expects($this->exactly(5))
            ->method('prepare')
            ->withConsecutive(
                ['DELETE FROM `score` WHERE group_id = :group_id'],
                ['DELETE FROM `player` WHERE group_id = :group_id'],
                ['DELETE FROM `member` WHERE group_id = :group_id'],
                ['DELETE FROM `follow` WHERE group_id = :group_id'],
                ['DELETE FROM `group` WHERE group_id = :group_id']
            )
            ->willReturn($this->pdoStatementMock);

        // execute method called once
        $this->pdoStatementMock
            ->expects($this->exactly(5))
            ->method('execute')
            ->with([':group_id' => $event->getAggregateId()]);

        $this->projection->projectGroupDeleted($event);
    }

    public function testItCanProjectGroupScoreUpdated()
    {
        $event = new GroupScoreUpdated(
            GroupId::fromString('groupId'),
            ScoreId::fromString('scoreId'),
            70,
            60,
            Date::fromDateTimeImmutable($this->getFakeTime()->currentTime())
        );

        // the pdo mock should call prepare and return a pdostatement mock
        $this->pdoMock
            ->expects($this->once())
            ->method('prepare')
            ->with('UPDATE `score` SET home_team_prediction = :home_team_prediction, away_team_prediction = :away_team_prediction
            WHERE score_id = :score_id')
            ->willReturn($this->pdoStatementMock);

        // execute method called once
        $this->pdoStatementMock
            ->expects($this->once())
            ->method('execute')
            ->with([
                ':score_id' => $event->getScoreId(),
                ':home_team_prediction' => $event->getHomeTeamPrediction(),
                ':away_team_prediction' => $event->getAwayTeamPrediction(),
            ]);

        $this->projection->projectGroupScoreUpdated($event);
    }

    public function testItCanProjectPlayerAdded()
    {
        $event = new PlayerAdded(
            GroupId::fromString('groupId'),
            PLayerId::fromString('playerId'),
            MemberId::fromString('memberId'),
            'username',
            Date::fromDateTimeImmutable($this->getFakeTime()->currentTime())
        );

        // the pdo mock should call prepare and return a pdostatement mock
        $this->pdoMock
            ->expects($this->once())
            ->method('prepare')
            ->with('INSERT INTO `player` (player_id, member_id, group_id, username, created_at)
            VALUES (:player_id, :member_id, :group_id, :username, :created_at)')
            ->willReturn($this->pdoStatementMock);

        // execute method called once
        $this->pdoStatementMock
            ->expects($this->once())
            ->method('execute')
            ->with([
                ':group_id' => $event->getAggregateId(),
                ':player_id' => $event->getPlayerId(),
                ':member_id' => $event->getMemberId(),
                ':username' => $event->getUsername(),
                ':created_at' => $event->getDateOccurred(),
            ]);

        $this->projection->projectPlayerAdded($event);
    }

    public function testItCanProjectTeamFollowed()
    {
        $event = new TeamFollowed(
            GroupId::fromString('groupId'),
            FollowId::fromString('followId'),
            TeamId::fromString('teamId'),
            SeasonId::fromString('seasonId'),
            Date::fromDateTimeImmutable($this->getFakeTime()->currentTime())
        );

        // the pdo mock should call prepare and return a pdostatement mock
        $this->pdoMock
            ->expects($this->once())
            ->method('prepare')
            ->with('INSERT INTO `follow` (follow_id, group_id, team_id, season_id, created_at)
            VALUES (:follow_id, :group_id, :team_id, :season_id, :created_at)')
            ->willReturn($this->pdoStatementMock);

        // execute method called once
        $this->pdoStatementMock
            ->expects($this->once())
            ->method('execute')
            ->with([
                ':follow_id' => $event->getFollowId(),
                ':group_id' => $event->getAggregateId(),
                ':team_id' => $event->getTeamId(),
                ':season_id' => $event->getSeasonId(),
                ':created_at' => $event->getDateOccurred(),
            ]);

        $this->projection->projectTeamFollowed($event);
    }

    public function testItCanProjectFollowDeleted()
    {
        $event = new FollowDeleted(GroupId::fromString('groupId'), FollowId::fromString('followId'), Date::fromDateTimeImmutable($this->getFakeTime()->currentTime()));

        // the pdo mock should call prepare and return a pdostatement mock
        $this->pdoMock
            ->expects($this->exactly(2))
            ->method('prepare')
            ->withConsecutive(
                ['DELETE FROM `follow` WHERE group_id = :group_id'],
                ['DELETE FROM `score` WHERE group_id = :group_id']
            )
            ->willReturn($this->pdoStatementMock);

        // execute method called once
        $this->pdoStatementMock
            ->expects($this->exactly(2))
            ->method('execute')
            ->with([':group_id' => $event->getAggregateId()]);

        $this->projection->projectFollowDeleted($event);
    }
}
