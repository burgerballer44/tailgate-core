<?php

namespace Tailgate\Tests\Infrastructure\Persistence\Projection\PDO;

use PHPUnit\Framework\TestCase;
use Tailgate\Domain\Model\Season\GameAdded;
use Tailgate\Domain\Model\Season\GameId;
use Tailgate\Domain\Model\Season\GameScoreUpdated;
use Tailgate\Domain\Model\Season\SeasonCreated;
use Tailgate\Domain\Model\Season\SeasonDeleted;
use Tailgate\Domain\Model\Season\SeasonUpdated;
use Tailgate\Domain\Model\Season\GameDeleted;
use Tailgate\Domain\Model\Season\SeasonId;
use Tailgate\Domain\Model\Team\TeamId;
use Tailgate\Infrastructure\Persistence\Projection\PDO\SeasonProjection;

class PDOSeasonProjectionTest extends TestCase
{
    private $pdoMock;
    private $pdoStatementMock;
    private $projection;

    public function setUp()
    {
        $this->pdoMock = $this->createMock(\PDO::class);
        $this->pdoStatementMock = $this->createMock(\PDOStatement::class);
        $this->projection = new SeasonProjection($this->pdoMock);
    }

    public function testItCanProjectSeasonCreated()
    {
        $event = new SeasonCreated(
            SeasonId::fromString('seasonId'),
            'sport',
            'season type',
            'name of season',
            '2019-09-01',
            '2019-12-28'
        );

        // the pdo mock should call prepare and return a pdostatement mock
        $this->pdoMock
            ->expects($this->once())
            ->method('prepare')
            ->with('INSERT INTO `season` (season_id, name, sport, type, season_start, season_end, created_at)
            VALUES (:season_id, :name, :sport, :type, :season_start, :season_end, :created_at)')
            ->willReturn($this->pdoStatementMock);

        // execute method called once
        $this->pdoStatementMock
            ->expects($this->once())
            ->method('execute')
            ->with([
                ':season_id' => $event->getAggregateId(),
                ':sport' => $event->getSport(),
                ':type' => $event->getSeasonType(),
                ':name' => $event->getName(),
                ':season_start' => $event->getSeasonStart(),
                ':season_end' => $event->getSeasonEnd(),
                ':created_at' => $event->getOccurredOn()->format('Y-m-d H:i:s')
            ]);

        $this->projection->projectSeasonCreated($event);
    }

    public function testItCanProjectGameAdded()
    {
        $event = new GameAdded(
            SeasonId::fromString('seasonId'),
            GameId::fromString('gameId'),
            TeamId::fromString('homeTeamId'),
            TeamId::fromString('awayTeamId'),
            '2019-10-01 19:30'
        );

        // the pdo mock should call prepare and return a pdostatement mock
        $this->pdoMock
            ->expects($this->once())
            ->method('prepare')
            ->with('INSERT INTO `game` (game_id, season_id, home_team_id, away_team_id, start_date, created_at)
            VALUES (:game_id, :season_id, :home_team_id, :away_team_id, :start_date, :created_at)')
            ->willReturn($this->pdoStatementMock);

        // execute method called once
        $this->pdoStatementMock
            ->expects($this->once())
            ->method('execute')
            ->with([
                ':season_id' => $event->getAggregateId(),
                ':game_id' => $event->getGameId(),
                ':home_team_id' => $event->getHomeTeamId(),
                ':away_team_id' => $event->getAwayTeamId(),
                ':start_date' => $event->getStartDate(),
                ':created_at' => $event->getOccurredOn()->format('Y-m-d H:i:s')
            ]);

        $this->projection->projectGameAdded($event);
    }


    public function testItCanProjectGameScoreUpdated()
    {
        $event = new GameScoreUpdated(
            SeasonId::fromString('seasonId'),
            GameId::fromString('gameId'),
            80,
            70,
            '2019-09-01 19:30'
        );

        // the pdo mock should call prepare and return a pdostatement mock
        $this->pdoMock
            ->expects($this->once())
            ->method('prepare')
            ->with('UPDATE `game` SET home_team_score = :home_team_score, away_team_score = :away_team_score, start_date = :start_date
            WHERE game_id = :game_id')
            ->willReturn($this->pdoStatementMock);

        // execute method called once
        $this->pdoStatementMock
            ->expects($this->once())
            ->method('execute')
            ->with([
                ':game_id' => $event->getGameId(),
                ':home_team_score' => $event->getHomeTeamScore(),
                ':away_team_score' => $event->getAwayTeamScore(),
                ':start_date' => $event->getStartDate()
            ]);

        $this->projection->projectGameScoreUpdated($event);
    }

    public function testItCanProjectGameDeleted()
    {
        $event = new GameDeleted(SeasonId::fromString('seasonId'), GameId::fromString('gameId'));

        // the pdo mock should call prepare and return a pdostatement mock
        $this->pdoMock
            ->expects($this->at(0))
            ->method('prepare')
            ->with('DELETE FROM `score` WHERE game_id = :game_id')
            ->willReturn($this->pdoStatementMock);
        $this->pdoMock
            ->expects($this->at(1))
            ->method('prepare')
            ->with('DELETE FROM `game` WHERE game_id = :game_id')
            ->willReturn($this->pdoStatementMock);

        // execute method called twice
        $this->pdoStatementMock
            ->expects($this->exactly(2))
            ->method('execute')
            ->with([':game_id' => $event->getGameId()]);

        $this->projection->projectGameDeleted($event);
    }

    public function testItCanProjectSeasonDeleted()
    {
        $event = new SeasonDeleted(SeasonId::fromString('seasonId'));

        // the pdo mock should call prepare and return a pdostatement mock
        $this->pdoMock
            ->expects($this->at(0))
            ->method('prepare')
            ->with('DELETE FROM `score` WHERE game_id IN
            (SELECT game_id FROM game WHERE season_id = :season_id)')
            ->willReturn($this->pdoStatementMock);
        $this->pdoMock
            ->expects($this->at(1))
            ->method('prepare')
            ->with('DELETE FROM `follow` WHERE season_id = :season_id')
            ->willReturn($this->pdoStatementMock);
        $this->pdoMock
            ->expects($this->at(2))
            ->method('prepare')
            ->with('DELETE FROM `game` WHERE season_id = :season_id')
            ->willReturn($this->pdoStatementMock);
        $this->pdoMock
            ->expects($this->at(3))
            ->method('prepare')
            ->with('DELETE FROM `season` WHERE season_id = :season_id')
            ->willReturn($this->pdoStatementMock);

        // execute method called three times
        $this->pdoStatementMock
            ->expects($this->exactly(4))
            ->method('execute')
            ->with([':season_id' => $event->getAggregateId()]);

        $this->projection->projectSeasonDeleted($event);
    }

    public function testItCanProjectSeasonUpdated()
    {
        $event = new SeasonUpdated(
            SeasonId::fromString('seasonId'),
            'sport',
            'season type',
            'name of season',
            '2019-09-01',
            '2019-12-28'
        );

        // the pdo mock should call prepare and return a pdostatement mock
        $this->pdoMock
            ->expects($this->once())
            ->method('prepare')
            ->with('UPDATE `season` SET name = :name, sport = :sport, type = :type, season_start = :season_start, season_end = :season_end
            WHERE season_id = :season_id')
            ->willReturn($this->pdoStatementMock);

        // execute method called once
        $this->pdoStatementMock
            ->expects($this->once())
            ->method('execute')
            ->with([
                ':season_id' => $event->getAggregateId(),
                ':sport' => $event->getSport(),
                ':type' => $event->getSeasonType(),
                ':name' => $event->getName(),
                ':season_start' => $event->getSeasonStart(),
                ':season_end' => $event->getSeasonEnd()
            ]);

        $this->projection->projectSeasonUpdated($event);
    }
}
