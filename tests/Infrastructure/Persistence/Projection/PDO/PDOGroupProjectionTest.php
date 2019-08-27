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
}
