<?php

namespace Tailgate\Tests\Infrastructure\Persistence\ViewRepository\PDO;

use RuntimeException;
use Tailgate\Domain\Model\Group\FollowId;
use Tailgate\Domain\Model\Group\GroupId;
use Tailgate\Domain\Model\Team\TeamId;
use Tailgate\Infrastructure\Persistence\ViewRepository\PDO\FollowViewRepository;
use Tailgate\Test\BaseTestCase;

class PDOFollowViewRepositoryTest extends BaseTestCase
{
    private $pdoMock;
    private $pdoStatementMock;
    private $viewRepository;

    public function setUp(): void
    {
        $this->pdoMock = $this->createMock(\PDO::class);
        $this->pdoStatementMock = $this->createMock(\PDOStatement::class);
        $this->viewRepository = new FollowViewRepository($this->pdoMock);
    }

    public function testFollowThatDoesNotExistReturnsException()
    {
        $followId = FollowId::fromString('followId');

        // the pdo mock should call prepare and return a pdostatement mock
        $this->pdoMock
            ->expects($this->once())
            ->method('prepare')
            ->with('SELECT `follow`.follow_id, `follow`.group_id, `follow`.team_id, `follow`.season_id, `group`.name as groupName, `team`.designation, `team`.mascot, `season`.name as seasonName
            FROM `follow`
            JOIN `group` on `group`.group_id = `follow`.group_id
            JOIN `team` on `team`.team_id = `follow`.team_id
            JOIN `season` on `season`.season_id = `follow`.season_id
            WHERE `follow`.follow_id = :follow_id LIMIT 1')
            ->willReturn($this->pdoStatementMock);

        // execute and fetch method called once
        $this->pdoStatementMock
            ->expects($this->once())
            ->method('execute')
            ->with([':follow_id' => (string) $followId]);
        $this->pdoStatementMock
            ->expects($this->once())
            ->method('fetch')
            ->willReturn(false);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Follow not found.');
        $this->viewRepository->get($followId);
    }

    public function testItCanGetAFollow()
    {
        $followId = FollowId::fromString('followId');

        // the pdo mock should call prepare and return a pdostatement mock
        $this->pdoMock
            ->expects($this->once())
            ->method('prepare')
            ->with('SELECT `follow`.follow_id, `follow`.group_id, `follow`.team_id, `follow`.season_id, `group`.name as groupName, `team`.designation, `team`.mascot, `season`.name as seasonName
            FROM `follow`
            JOIN `group` on `group`.group_id = `follow`.group_id
            JOIN `team` on `team`.team_id = `follow`.team_id
            JOIN `season` on `season`.season_id = `follow`.season_id
            WHERE `follow`.follow_id = :follow_id LIMIT 1')
            ->willReturn($this->pdoStatementMock);

        // execute method called once
        $this->pdoStatementMock
            ->expects($this->once())
            ->method('execute')
            ->with([':follow_id' => (string) $followId]);
        $this->pdoStatementMock
            ->expects($this->once())
            ->method('fetch')
            ->willReturn([
                'group_id' => 'blah',
                'follow_id' => 'blah',
                'team_id' => 'blah',
                'season_id' => 'blah',
                'groupName' => 'blah',
                'designation' => 'blah',
                'mascot' => 'blah',
                'seasonName' => 'blah',
            ]);

        $this->viewRepository->get($followId);
    }

    public function testItCanGetAllFollowsOfATeam()
    {
        $teamId = TeamId::fromString('teamId');

        // the pdo mock should call prepare and return a pdostatement mock
        $this->pdoMock
            ->expects($this->once())
            ->method('prepare')
            ->with('SELECT `follow`.follow_id, `follow`.group_id, `follow`.team_id, `follow`.season_id, `group`.name as groupName, `team`.designation, `team`.mascot, `season`.name as seasonName
            FROM `follow`
            JOIN `group` on `group`.group_id = `follow`.group_id
            JOIN `team` on `team`.team_id = `follow`.team_id
            JOIN `season` on `season`.season_id = `follow`.season_id
            WHERE `follow`.team_id = :team_id')
            ->willReturn($this->pdoStatementMock);

        // execute method called once
        $this->pdoStatementMock
            ->expects($this->once())
            ->method('execute')
            ->with([':team_id' => (string) $teamId]);

        // fetch method called
        $this->pdoStatementMock
            ->expects($this->atLeastOnce())
            ->method('fetch');

        $this->viewRepository->getAllByTeam($teamId);
    }

    public function testItCanGetAFollowForAGroup()
    {
        $groupId = GroupId::fromString('groupId');

        // the pdo mock should call prepare and return a pdostatement mock
        $this->pdoMock
            ->expects($this->once())
            ->method('prepare')
            ->with('SELECT `follow`.follow_id, `follow`.group_id, `follow`.team_id, `follow`.season_id, `group`.name as groupName, `team`.designation, `team`.mascot, `season`.name as seasonName
            FROM `follow`
            JOIN `group` on `group`.group_id = `follow`.group_id
            JOIN `team` on `team`.team_id = `follow`.team_id
            JOIN `season` on `season`.season_id = `follow`.season_id
            WHERE `follow`.group_id = :group_id
            LIMIT 1')
            ->willReturn($this->pdoStatementMock);

        // execute method called once
        $this->pdoStatementMock
            ->expects($this->once())
            ->method('execute')
            ->with([':group_id' => (string) $groupId]);

        // fetch method called
        $this->pdoStatementMock
            ->expects($this->atLeastOnce())
            ->method('fetch');

        $this->viewRepository->getByGroup($groupId);
    }
}
