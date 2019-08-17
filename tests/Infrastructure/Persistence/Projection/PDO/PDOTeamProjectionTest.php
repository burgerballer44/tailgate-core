<?php

namespace Tailgate\Tests\Infrastructure\Persistence\Projection\PDO;

use PHPUnit\Framework\TestCase;
use Tailgate\Domain\Model\Team\FollowId;
use Tailgate\Domain\Model\Group\GroupId;
use Tailgate\Domain\Model\Team\TeamAdded;
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

    public function testItCanProjectTeamFollowed()
    {
        $event = new TeamFollowed(
            FollowId::fromString('followId'),
            TeamId::fromString('teamId'),
            GroupId::fromString('groupId')
        );

        // the pdo mock should call prepare and return a pdostatement mock
        $this->pdoMock
            ->expects($this->once())
            ->method('prepare')
            ->with('INSERT INTO `follow` (follow_id, team_id, group_id)
            VALUES (:follow_id, :team_id, :group_id)')
            ->willReturn($this->pdoStatementMock);

        // execute method called once
        $this->pdoStatementMock
            ->expects($this->once())
            ->method('execute')
            ->with([
                ':follow_id' => $event->getFollowId(),
                ':group_id' => $event->getGroupId(),
                ':team_id' => $event->getTeamId(),
                ':created_at' => $event->getOccurredOn()->format('Y-m-d H:i:s')
            ]);

        $this->projection->projectTeamFollowed($event);
    }
}
