<?php

namespace Tailgate\Tests\Infrastructure\Persistence\Projection\PDO;

use PHPUnit\Framework\TestCase;
use Tailgate\Domain\Model\Season\GameId;
use Tailgate\Domain\Model\Group\GroupId;
use Tailgate\Domain\Model\Group\GroupCreated;
use Tailgate\Domain\Model\Group\MemberAdded;
use Tailgate\Domain\Model\Group\MemberId;
use Tailgate\Domain\Model\Group\ScoreId;
use Tailgate\Domain\Model\Group\ScoreSubmitted;
use Tailgate\Domain\Model\Group\GroupUpdated;
use Tailgate\Domain\Model\Group\MemberDeleted;
use Tailgate\Domain\Model\Group\ScoreDeleted;
use Tailgate\Domain\Model\Group\GroupScoreUpdated;
use Tailgate\Domain\Model\Group\GroupDeleted;
use Tailgate\Domain\Model\Group\PlayerId;
use Tailgate\Domain\Model\Group\PlayerAdded;
use Tailgate\Domain\Model\User\UserId;
use Tailgate\Infrastructure\Persistence\Projection\PDO\GroupProjection;

class PDOGroupProjectionTest extends TestCase
{
    private $pdoMock;
    private $pdoStatementMock;
    private $projection;

    public function setUp()
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
            UserId::fromString('userId')
        );

        // the pdo mock should call prepare and return a pdostatement mock
        $this->pdoMock
            ->expects($this->once())
            ->method('prepare')
            ->with('INSERT INTO `group` (group_id, name, owner_id, created_at)
            VALUES (:group_id, :name, :owner_id, :created_at)')
            ->willReturn($this->pdoStatementMock);

        // execute method called once
        $this->pdoStatementMock
            ->expects($this->once())
            ->method('execute')
            ->with([
                ':group_id' => $event->getAggregateId(),
                ':name' => $event->getName(),
                ':owner_id' => $event->getOwnerId(),
                ':created_at' => $event->getOccurredOn()->format('Y-m-d H:i:s')
            ]);

        $this->projection->projectGroupCreated($event);
    }

    public function testItCanProjectMemberAdded()
    {
        $event = new MemberAdded(
            GroupId::fromString('groupId'),
            MemberId::fromString('memberId'),
            UserId::fromString('userId'),
            'role'
        );

        // the pdo mock should call prepare and return a pdostatement mock
        $this->pdoMock
            ->expects($this->once())
            ->method('prepare')
            ->with('INSERT INTO `member` (member_id, group_id, user_id, role, created_at)
            VALUES (:member_id, :group_id, :user_id, :role, :created_at)')
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
                ':created_at' => $event->getOccurredOn()->format('Y-m-d H:i:s')
            ]);

        $this->projection->projectMemberAdded($event);
    }


    public function testItCanProjectScoreSubmitted()
    {
        $event = new ScoreSubmitted(
            GroupId::fromString('groupId'),
            ScoreId::fromString('scoreId'),
            UserId::fromString('userId'),
            GameId::fromString('gameId'),
            80,
            70
        );

        // the pdo mock should call prepare and return a pdostatement mock
        $this->pdoMock
            ->expects($this->once())
            ->method('prepare')
            ->with('INSERT INTO `score` (score_id, group_id, user_id, game_id, home_team_prediction, away_team_prediction, created_at)
            VALUES (:score_id, :group_id, :user_id, :game_id, :home_team_prediction, :away_team_prediction, :created_at)')
            ->willReturn($this->pdoStatementMock);

        // execute method called once
        $this->pdoStatementMock
            ->expects($this->once())
            ->method('execute')
            ->with([
                ':group_id' => $event->getAggregateId(),
                ':score_id' => $event->getScoreId(),
                ':user_id' => $event->getUserId(),
                ':game_id' => $event->getGameId(),
                ':home_team_prediction' => $event->getHomeTeamPrediction(),
                ':away_team_prediction' => $event->getAwayTeamPrediction(),
                ':created_at' => $event->getOccurredOn()->format('Y-m-d H:i:s')
            ]);

        $this->projection->projectScoreSubmitted($event);
    }

    public function testItCanProjectGroupUpdated()
    {
        $event = new GroupUpdated(
            GroupId::fromString('groupId'),
            'name',
            UserId::fromString('userId')
        );

        // the pdo mock should call prepare and return a pdostatement mock
        $this->pdoMock
            ->expects($this->once())
            ->method('prepare')
            ->with('UPDATE `group` (name, owner_id)
            VALUES (:name, :owner_id)
            WHERE :group_id = group_id')
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

    public function testItCanProjectMemberDeleted()
    {
        $event = new MemberDeleted(
            GroupId::fromString('groupId'),
            MemberId::fromString('memberId')
        );

        // the pdo mock should call prepare and return a pdostatement mock
        $this->pdoMock
            ->expects($this->once())
            ->method('prepare')
            ->with('DELETE FROM `member` WHERE :member_id = member_id')
            ->willReturn($this->pdoStatementMock);

        // execute method called once
        $this->pdoStatementMock
            ->expects($this->once())
            ->method('execute')
            ->with([':member_id' => $event->getMemberId()]);

        $this->projection->projectMemberDeleted($event);
    }

    public function testItCanProjectScoreDeleted()
    {
        $event = new ScoreDeleted(
            GroupId::fromString('groupId'),
            ScoreId::fromString('scoreId')
        );

        // the pdo mock should call prepare and return a pdostatement mock
        $this->pdoMock
            ->expects($this->once())
            ->method('prepare')
            ->with('DELETE FROM `score` WHERE :score_id = score_id')
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
        $event = new GroupDeleted(GroupId::fromString('groupId'));

        // the pdo mock should call prepare and return a pdostatement mock
        $this->pdoMock
            ->expects($this->at(0))
            ->method('prepare')
            ->with('DELETE FROM `score` WHERE :group_id = group_id')
            ->willReturn($this->pdoStatementMock);
        $this->pdoMock
            ->expects($this->at(1))
            ->method('prepare')
            ->with('DELETE FROM `member` WHERE :group_id = group_id')
            ->willReturn($this->pdoStatementMock);
        $this->pdoMock
            ->expects($this->at(2))
            ->method('prepare')
            ->with('DELETE FROM `group` WHERE :group_id = group_id')
            ->willReturn($this->pdoStatementMock);

        // execute method called once
        $this->pdoStatementMock
            ->expects($this->exactly(3))
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
            60
        );

        // the pdo mock should call prepare and return a pdostatement mock
        $this->pdoMock
            ->expects($this->once())
            ->method('prepare')
            ->with('UPDATE `score` (home_team_prediction, away_team_prediction)
            VALUES (:home_team_prediction, :away_team_prediction)
            WHERE :score_id = score_id')
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
            'username'
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
                ':created_at' => (new \DateTimeImmutable())->format('Y-m-d H:i:s')
            ]);

        $this->projection->projectPlayerAdded($event);
    }
}
