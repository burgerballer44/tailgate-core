<?php

namespace Tailgate\Tests\Infrastructure\Persistence\ViewRepository\PDO;

use PHPUnit\Framework\TestCase;
use Tailgate\Domain\Model\Season\SeasonId;
use Tailgate\Domain\Model\Season\SeasonView;
use Tailgate\Infrastructure\Persistence\ViewRepository\PDO\SeasonViewRepository;

class PDOSeasonViewRepositoryTest extends TestCase
{
    private $pdoMock;
    private $pdoStatementMock;
    private $viewRepository;

    public function setUp()
    {
        $this->pdoMock = $this->createMock(\PDO::class);
        $this->pdoStatementMock = $this->createMock(\PDOStatement::class);
        $this->viewRepository = new SeasonViewRepository($this->pdoMock);
    }

    public function testItCanGetAUser()
    {
        $seasonId = SeasonId::fromString('seasonId');

        // the pdo mock should call prepare and return a pdostatement mock
        $this->pdoMock
            ->expects($this->once())
            ->method('prepare')
            ->with('SELECT * FROM `season` WHERE season_id = :season_id')
            ->willReturn($this->pdoStatementMock);

        // execute method called once
        $this->pdoStatementMock
            ->expects($this->once())
            ->method('execute')
            ->with([':season_id' => (string) $seasonId]);

        $this->viewRepository->get($seasonId);
    }

    public function testItCanGetAllUsers()
    {
        $seasonId = SeasonId::fromString('seasonId');

        // the pdo mock should call prepare and return a pdostatement mock
        $this->pdoMock
            ->expects($this->once())
            ->method('prepare')
            ->with('SELECT * FROM `season`')
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
