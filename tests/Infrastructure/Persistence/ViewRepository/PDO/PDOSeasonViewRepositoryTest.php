<?php

namespace Tailgate\Tests\Infrastructure\Persistence\ViewRepository\PDO;

use RuntimeException;
use Tailgate\Domain\Model\Season\SeasonId;
use Tailgate\Domain\Model\Season\Sport;
use Tailgate\Infrastructure\Persistence\ViewRepository\PDO\SeasonViewRepository;
use Tailgate\Test\BaseTestCase;

class PDOSeasonViewRepositoryTest extends BaseTestCase
{
    private $pdoMock;
    private $pdoStatementMock;
    private $viewRepository;

    public function setUp(): void
    {
        $this->pdoMock = $this->createMock(\PDO::class);
        $this->pdoStatementMock = $this->createMock(\PDOStatement::class);
        $this->viewRepository = new SeasonViewRepository($this->pdoMock);
    }

    public function testSeasonThatDoesNotExistReturnsException()
    {
        $seasonId = SeasonId::fromString('seasonId');

        // the pdo mock should call prepare and return a pdostatement mock
        $this->pdoMock
            ->expects($this->once())
            ->method('prepare')
            ->with('SELECT * FROM `season` WHERE season_id = :season_id LIMIT 1')
            ->willReturn($this->pdoStatementMock);

        // execute and fetch method called once
        $this->pdoStatementMock
            ->expects($this->once())
            ->method('execute')
            ->with([':season_id' => (string) $seasonId]);
        $this->pdoStatementMock
            ->expects($this->once())
            ->method('fetch')
            ->willReturn(false);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Season not found.');
        $this->viewRepository->get($seasonId);
    }

    public function testItCanGetASeason()
    {
        $seasonId = SeasonId::fromString('seasonId');

        // the pdo mock should call prepare and return a pdostatement mock
        $this->pdoMock
            ->expects($this->once())
            ->method('prepare')
            ->with('SELECT * FROM `season` WHERE season_id = :season_id LIMIT 1')
            ->willReturn($this->pdoStatementMock);

        // execute method called once
        $this->pdoStatementMock
            ->expects($this->once())
            ->method('execute')
            ->with([':season_id' => (string) $seasonId]);
        $this->pdoStatementMock
            ->expects($this->once())
            ->method('fetch')
            ->willReturn([
                'season_id' => 'blah',
                'sport' => 'blah',
                'type' => 'blah',
                'name' => 'blah',
                'season_start' => 'blah',
                'season_end' => 'blah',
            ]);

        $this->viewRepository->get($seasonId);
    }

    public function testItCanGetSeasonsBySport()
    {
        $sport = Sport::getFootball();

        // the pdo mock should call prepare and return a pdostatement mock
        $this->pdoMock
            ->expects($this->once())
            ->method('prepare')
            ->with('SELECT * FROM `season` WHERE sport = :sport')
            ->willReturn($this->pdoStatementMock);

        // execute method called once
        $this->pdoStatementMock
            ->expects($this->once())
            ->method('execute')
            ->with([':sport' => (string) $sport]);
        $this->pdoStatementMock
            ->expects($this->once())
            ->method('fetch');

        $this->viewRepository->allBySport($sport);
    }

    public function testItCanGetAllSeasons()
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
