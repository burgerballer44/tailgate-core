<?php

namespace Tailgate\Tests\Infrastructure\Persistence\ViewRepository\PDO;

use PHPUnit\Framework\TestCase;
use Tailgate\Domain\Model\Team\TeamId;
use Tailgate\Domain\Model\Team\TeamView;
use Tailgate\Infrastructure\Persistence\ViewRepository\PDO\TeamViewRepository;

class PDOTeamViewRepositoryTest extends TestCase
{
    private $pdoMock;
    private $pdoStatementMock;
    private $viewRepository;

    public function setUp()
    {
        $this->pdoMock = $this->createMock(\PDO::class);
        $this->pdoStatementMock = $this->createMock(\PDOStatement::class);
        $this->viewRepository = new TeamViewRepository($this->pdoMock);
    }

    public function testItCanGetAUser()
    {
        $teamId = TeamId::fromString('teamId');

        // the pdo mock should call prepare and return a pdostatement mock
        $this->pdoMock
            ->expects($this->once())
            ->method('prepare')
            ->with('SELECT * FROM `team` WHERE team_id = :team_id')
            ->willReturn($this->pdoStatementMock);

        // execute method called once
        $this->pdoStatementMock
            ->expects($this->once())
            ->method('execute')
            ->with([':team_id' => (string) $teamId]);

        $this->viewRepository->get($teamId);
    }

    public function testItCanGetAllUsers()
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
