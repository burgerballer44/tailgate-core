<?php

namespace Tailgate\Tests\Infrastructure\Persistence\Projection\PDO;

use PHPUnit\Framework\TestCase;
use Tailgate\Domain\Model\Team\FollowId;
use Tailgate\Domain\Model\Group\GroupId;
use Tailgate\Domain\Model\Team\TeamAdded;
use Tailgate\Domain\Model\Team\TeamUpdated;
use Tailgate\Domain\Model\Team\FollowDeleted;
use Tailgate\Domain\Model\Team\TeamDeleted;
use Tailgate\Domain\Model\Team\TeamId;
use Tailgate\Domain\Model\Team\TeamFollowed;
use Tailgate\Infrastructure\Persistence\Projection\PDO\TeamProjection;

class PDOTeamProjectionTest extends TestCase
{
    private $pdoMock;
    private $pdoStatementMock;
    private $projection;

    public function setUp()
    {
        $this->pdoMock = $this->createMock(\PDO::class);
        $this->pdoStatementMock = $this->createMock(\PDOStatement::class);
        $this->projection = new TeamProjection($this->pdoMock);
    }

    public function testItCanProjectTeamAdded()
    {
        $event = new TeamAdded(TeamId::fromString('teamId'), 'designation', 'mascot');

        // the pdo mock should call prepare and return a pdostatement mock
        $this->pdoMock
            ->expects($this->once())
            ->method('prepare')
            ->with('INSERT INTO `team` (team_id, designation, mascot, created_at)
            VALUES (:team_id, :designation, :mascot, :created_at)')
            ->willReturn($this->pdoStatementMock);

        // execute method called once
        $this->pdoStatementMock
            ->expects($this->once())
            ->method('execute')
            ->with([
                ':team_id' => $event->getAggregateId(),
                ':designation' => $event->getDesignation(),
                ':mascot' => $event->getMascot(),
                ':created_at' => $event->getOccurredOn()->format('Y-m-d H:i:s')
            ]);

        $this->projection->projectTeamAdded($event);
    }

    public function testItCanProjectTeamUpdated()
    {
        $event = new TeamUpdated(TeamId::fromString('teamId'), 'designation', 'mascot');

        // the pdo mock should call prepare and return a pdostatement mock
        $this->pdoMock
            ->expects($this->once())
            ->method('prepare')
            ->with('UPDATE `team`
            SET designation = :designation, mascot = :mascot
            WHERE team_id = :team_id')
            ->willReturn($this->pdoStatementMock);

        // execute method called once
        $this->pdoStatementMock
            ->expects($this->once())
            ->method('execute')
            ->with([
                ':team_id' => $event->getAggregateId(),
                ':designation' => $event->getDesignation(),
                ':mascot' => $event->getMascot()
            ]);

        $this->projection->projectTeamUpdated($event);
    }

    public function testItCanProjectTeamFollowed()
    {
        $event = new TeamFollowed(
            TeamId::fromString('teamId'),
            FollowId::fromString('followId'),
            GroupId::fromString('groupId')
        );

        // the pdo mock should call prepare and return a pdostatement mock
        $this->pdoMock
            ->expects($this->once())
            ->method('prepare')
            ->with('INSERT INTO `follow` (follow_id, team_id, group_id, created_at)
            VALUES (:follow_id, :team_id, :group_id, :created_at)')
            ->willReturn($this->pdoStatementMock);

        // execute method called once
        $this->pdoStatementMock
            ->expects($this->once())
            ->method('execute')
            ->with([
                ':follow_id' => $event->getFollowId(),
                ':group_id' => $event->getGroupId(),
                ':team_id' => $event->getAggregateId(),
                ':created_at' => $event->getOccurredOn()->format('Y-m-d H:i:s')
            ]);

        $this->projection->projectTeamFollowed($event);
    }

    public function testItCanProjectFollowDeleted()
    {
        $event = new FollowDeleted(TeamId::fromString('teamId'), FollowId::fromString('followId'));

        // the pdo mock should call prepare and return a pdostatement mock
        $this->pdoMock
            ->expects($this->once())
            ->method('prepare')
            ->with('DELETE FROM `follow` WHERE follow_id = :follow_id')
            ->willReturn($this->pdoStatementMock);

        // execute method called once
        $this->pdoStatementMock
            ->expects($this->once())
            ->method('execute')
            ->with([':follow_id' => $event->getFollowId()]);

        $this->projection->projectFollowDeleted($event);
    }

    public function testItCanProjectTeamDeleted()
    {
        $event = new TeamDeleted(TeamId::fromString('teamId'), FollowId::fromString('followId'));

        // the pdo mock should call prepare and return a pdostatement mock
        $this->pdoMock
            ->expects($this->at(0))
            ->method('prepare')
            ->with('DELETE FROM `score` WHERE game_id IN (
            SELECT game_id FROM game WHERE home_team_id = :team_id OR away_team_id = :team_id
        )')
            ->willReturn($this->pdoStatementMock);
        $this->pdoMock
            ->expects($this->at(1))
            ->method('prepare')
            ->with('DELETE FROM `game` WHERE home_team_id = :team_id OR away_team_id = :team_id')
            ->willReturn($this->pdoStatementMock);
        $this->pdoMock
            ->expects($this->at(2))
            ->method('prepare')
            ->with('DELETE FROM `follow` WHERE team_id = :team_id')
            ->willReturn($this->pdoStatementMock);
        $this->pdoMock
            ->expects($this->at(3))
            ->method('prepare')
            ->with('DELETE FROM `team` WHERE team_id = :team_id')
            ->willReturn($this->pdoStatementMock);

        // execute method called 4 times
        $this->pdoStatementMock
            ->expects($this->exactly(4))
            ->method('execute')
            ->with([':team_id' => $event->getAggregateId()]);

        $this->projection->projectTeamDeleted($event);
    }
}
