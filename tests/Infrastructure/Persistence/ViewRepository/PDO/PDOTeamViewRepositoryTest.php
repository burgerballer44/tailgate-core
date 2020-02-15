<?php

namespace Tailgate\Tests\Infrastructure\Persistence\ViewRepository\PDO;

use PHPUnit\Framework\TestCase;
use Tailgate\Domain\Model\Team\TeamId;
use Tailgate\Domain\Model\Team\TeamView;
use Tailgate\Infrastructure\Persistence\ViewRepository\PDO\TeamViewRepository;
use RuntimeException;

class PDOTeamViewRepositoryTest extends TestCase
{
    private $pdoMock;
    private $pdoStatementMock;
    private $viewRepository;

    public function setUp(): void
    {
        $this->pdoMock = $this->createMock(\PDO::class);
        $this->pdoStatementMock = $this->createMock(\PDOStatement::class);
        $this->viewRepository = new TeamViewRepository($this->pdoMock);
    }

    public function testTeamThatDoesNotExistReturnsException()
    {
        $teamId = TeamId::fromString('teamId');

        // the pdo mock should call prepare and return a pdostatement mock
        $this->pdoMock
            ->expects($this->once())
            ->method('prepare')
            ->with('SELECT * FROM `team` WHERE team_id = :team_id LIMIT 1')
            ->willReturn($this->pdoStatementMock);

        // execute and fetch method called once
        $this->pdoStatementMock
            ->expects($this->once())
            ->method('execute')
            ->with([':team_id' => (string) $teamId]);
        $this->pdoStatementMock
            ->expects($this->once())
            ->method('fetch')
            ->willReturn(false);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Team not found.');
        $this->viewRepository->get($teamId);
    }

    public function testItCanGetATeam()
    {
        $teamId = TeamId::fromString('teamId');

        // the pdo mock should call prepare and return a pdostatement mock
        $this->pdoMock
            ->expects($this->once())
            ->method('prepare')
            ->with('SELECT * FROM `team` WHERE team_id = :team_id LIMIT 1')
            ->willReturn($this->pdoStatementMock);

        // execute method called once
        $this->pdoStatementMock
            ->expects($this->once())
            ->method('execute')
            ->with([':team_id' => (string) $teamId]);
        $this->pdoStatementMock
            ->expects($this->once())
            ->method('fetch')
            ->willReturn([
                'team_id' => 'blah',
                'designation' => 'blah',
                'mascot' => 'blah',
            ]);

        $this->viewRepository->get($teamId);
    }

    public function testItCanGetAllTeams()
    {
        $teamId = TeamId::fromString('teamId');

        // the pdo mock should call prepare and return a pdostatement mock
        $this->pdoMock
            ->expects($this->once())
            ->method('prepare')
            ->with('SELECT * FROM `team`')
            ->willReturn($this->pdoStatementMock);

        // execute method called once
        $this->pdoStatementMock
            ->expects($this->once())
            ->method('execute');

        // fetch method called
        $this->pdoStatementMock
            ->expects($this->atLeastOnce())
            ->method('fetch');

        $this->viewRepository->all();
    }
}
