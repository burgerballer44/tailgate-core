<?php

namespace Tailgate\Tests\Infrastructure\Persistence\ViewRepository\PDO;

use PHPUnit\Framework\TestCase;
use Tailgate\Domain\Model\Season\SeasonId;
use Tailgate\Domain\Model\Season\GameId;
use Tailgate\Infrastructure\Persistence\ViewRepository\PDO\GameViewRepository;
use Tailgate\Infrastructure\Persistence\ViewRepository\RepositoryException;

class PDOGameViewRepositoryTest extends TestCase
{
    private $pdoMock;
    private $pdoStatementMock;
    private $viewRepository;

    public function setUp()
    {
        $this->pdoMock = $this->createMock(\PDO::class);
        $this->pdoStatementMock = $this->createMock(\PDOStatement::class);
        $this->viewRepository = new GameViewRepository($this->pdoMock);
    }

    public function testFollowThatDoesNotExistReturnsException()
    {
        $gameId = GameId::fromString('followId');

        // the pdo mock should call prepare and return a pdostatement mock
        $this->pdoMock
            ->expects($this->once())
            ->method('prepare')
            ->with('SELECT * FROM `game` WHERE game_id = :game_id LIMIT 1')
            ->willReturn($this->pdoStatementMock);

        // execute and fetch method called once
        $this->pdoStatementMock
            ->expects($this->once())
            ->method('execute')
            ->with([':game_id' => (string) $gameId]);
        $this->pdoStatementMock
            ->expects($this->once())
            ->method('fetch')
            ->willReturn(false);

        $this->expectException(RepositoryException::class);
        $this->expectExceptionMessage('Game not found.');
        $this->viewRepository->get($gameId);
    }

    public function testItCanGetAFollow()
    {
        $gameId = GameId::fromString('followId');

        // the pdo mock should call prepare and return a pdostatement mock
        $this->pdoMock
            ->expects($this->once())
            ->method('prepare')
            ->with('SELECT * FROM `game` WHERE game_id = :game_id LIMIT 1')
            ->willReturn($this->pdoStatementMock);

        // execute method called once
        $this->pdoStatementMock
            ->expects($this->once())
            ->method('execute')
            ->with([':game_id' => (string) $gameId]);
        $this->pdoStatementMock
            ->expects($this->once())
            ->method('fetch')
            ->willReturn([
                'season_id' => 'blah',
                'game_id' => 'blah',
                'home_team_id' => 'blah',
                'away_team_id' => 'blah',
                'home_team_score' => 'blah',
                'away_team_score' => 'blah',
                'start_date' => 'blah',
            ]);

        $this->viewRepository->get($gameId);
    }

    public function testItCanGetAllGamesOfASeason()
    {
        $seasonId = SeasonId::fromString('seasonId');

        // the pdo mock should call prepare and return a pdostatement mock
        $this->pdoMock
            ->expects($this->once())
            ->method('prepare')
            ->with('SELECT * FROM `game` WHERE season_id = :season_id')
            ->willReturn($this->pdoStatementMock);

        // execute method called once
        $this->pdoStatementMock
            ->expects($this->once())
            ->method('execute')
            ->with([':season_id' => (string) $seasonId]);

        // fetch method called
        $this->pdoStatementMock
            ->expects($this->atLeastOnce())
            ->method('fetch');

        $this->viewRepository->getAllBySeason($seasonId);
    }
}
