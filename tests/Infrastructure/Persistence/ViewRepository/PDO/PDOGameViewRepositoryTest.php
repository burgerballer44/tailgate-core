<?php

namespace Tailgate\Tests\Infrastructure\Persistence\ViewRepository\PDO;

use Tailgate\Test\BaseTestCase;
use Tailgate\Domain\Model\Team\TeamId;
use Tailgate\Domain\Model\Season\SeasonId;
use Tailgate\Domain\Model\Season\GameId;
use Tailgate\Infrastructure\Persistence\ViewRepository\PDO\GameViewRepository;
use RuntimeException;

class PDOGameViewRepositoryTest extends BaseTestCase
{
    private $pdoMock;
    private $pdoStatementMock;
    private $viewRepository;

    public function setUp(): void
    {
        $this->pdoMock = $this->createMock(\PDO::class);
        $this->pdoStatementMock = $this->createMock(\PDOStatement::class);
        $this->viewRepository = new GameViewRepository($this->pdoMock);
    }

    public function testGameThatDoesNotExistReturnsException()
    {
        $gameId = GameId::fromString('followId');

        // the pdo mock should call prepare and return a pdostatement mock
        $this->pdoMock
            ->expects($this->once())
            ->method('prepare')
            ->with('SELECT g.season_id, g.game_id, g.home_team_id, g.away_team_id, g.home_team_score, g.away_team_score, g.start_date, g.start_time, hot.designation as home_designation, hot.mascot as home_mascot, awt.designation as away_designation, awt.mascot as away_mascot
            FROM `game` g 
            JOIN `team` hot on hot.team_id = g.home_team_id
            JOIN `team` awt on awt.team_id = g.away_team_id
            WHERE g.game_id = :game_id LIMIT 1')
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

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Game not found.');
        $this->viewRepository->get($gameId);
    }

    public function testItCanGetAGame()
    {
        $gameId = GameId::fromString('followId');

        // the pdo mock should call prepare and return a pdostatement mock
        $this->pdoMock
            ->expects($this->once())
            ->method('prepare')
            ->with('SELECT g.season_id, g.game_id, g.home_team_id, g.away_team_id, g.home_team_score, g.away_team_score, g.start_date, g.start_time, hot.designation as home_designation, hot.mascot as home_mascot, awt.designation as away_designation, awt.mascot as away_mascot
            FROM `game` g 
            JOIN `team` hot on hot.team_id = g.home_team_id
            JOIN `team` awt on awt.team_id = g.away_team_id
            WHERE g.game_id = :game_id LIMIT 1')
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
                'start_time' => 'blah',
                'home_designation' => 'blah',
                'home_mascot' => 'blah',
                'away_designation' => 'blah',
                'away_mascot' => 'blah'
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
            ->with('SELECT g.season_id, g.game_id, g.home_team_id, g.away_team_id, g.home_team_score, g.away_team_score, g.start_date, g.start_time, hot.designation as home_designation, hot.mascot as home_mascot, awt.designation as away_designation, awt.mascot as away_mascot
            FROM `game` g 
            JOIN `team` hot on hot.team_id = g.home_team_id
            JOIN `team` awt on awt.team_id = g.away_team_id
            WHERE g.season_id = :season_id
            ORDER BY g.start_date')
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

    public function testItCanGetAllGamesOfATeam()
    {
        $teamId = TeamId::fromString('teamId');

        // the pdo mock should call prepare and return a pdostatement mock
        $this->pdoMock
            ->expects($this->once())
            ->method('prepare')
            ->with('SELECT g.season_id, g.game_id, g.home_team_id, g.away_team_id, g.home_team_score, g.away_team_score, g.start_date, g.start_time, hot.designation as home_designation, hot.mascot as home_mascot, awt.designation as away_designation, awt.mascot as away_mascot
            FROM `game` g
            JOIN `team` hot on hot.team_id = g.home_team_id
            JOIN `team` awt on awt.team_id = g.away_team_id
            WHERE g.home_team_id = :team_id OR g.away_team_id = :team_id
            ORDER BY g.start_date')
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

        $this->viewRepository->getAllByteam($teamId);
    }

    public function testItCanGetAllGamesOfATeamAndSeason()
    {
        $teamId = TeamId::fromString('teamId');
        $seasonId = SeasonId::fromString('seasonId');

        // the pdo mock should call prepare and return a pdostatement mock
        $this->pdoMock
            ->expects($this->once())
            ->method('prepare')
            ->with('SELECT g.season_id, g.game_id, g.home_team_id, g.away_team_id, g.home_team_score, g.away_team_score, g.start_date, g.start_time, hot.designation as home_designation, hot.mascot as home_mascot, awt.designation as away_designation, awt.mascot as away_mascot
            FROM `game` g 
            JOIN `team` hot on hot.team_id = g.home_team_id
            JOIN `team` awt on awt.team_id = g.away_team_id
            WHERE g.season_id = :season_id
            AND (g.home_team_id = :team_id OR g.away_team_id = :team_id)
            ORDER BY g.start_date')
            ->willReturn($this->pdoStatementMock);

        // execute method called once
        $this->pdoStatementMock
            ->expects($this->once())
            ->method('execute')
            ->with([':team_id' => (string) $teamId, ':season_id' => (string) $seasonId]);

        // fetch method called
        $this->pdoStatementMock
            ->expects($this->atLeastOnce())
            ->method('fetch');

        $this->viewRepository->getAllByTeamAndSeason($teamId, $seasonId);
    }
}
