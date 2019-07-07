<?php

namespace Tailgate\Tests\Infrastructure\Persistence\Projection\PDO;

use PHPUnit\Framework\TestCase;
use Tailgate\Domain\Model\Season\GameAdded;
use Tailgate\Domain\Model\Season\GameId;
use Tailgate\Domain\Model\Season\GameScoreAdded;
use Tailgate\Domain\Model\Season\SeasonCreated;
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
            \DateTimeImmutable::createFromFormat('Y-m-d', '2019-09-01'),
            \DateTimeImmutable::createFromFormat('Y-m-d', '2019-12-28')
        );

        // the pdo mock should call prepare and return a pdostatement mock
        $this->pdoMock
            ->expects($this->once())
            ->method('prepare')
            ->with('INSERT INTO `season` (season_id, sport, type, name, season_start, season_end, created_at)
            VALUES (:season_id, :sport, :type, :name, :season_start, :season_end, :created_at)')
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
            GameId::fromString('gameId'),
            SeasonId::fromString('seasonId'),
            TeamId::fromString('homeTeamId'),
            TeamId::fromString('awayTeamId'),
            \DateTimeImmutable::createFromFormat('Y-m-d', '2019-10-01')
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


    public function testItCanProjectGameScoreAdded()
    {
        $event = new GameScoreAdded(
            GameId::fromString('gameId'),
            SeasonId::fromString('seasonId'),
            80,
            70
        );

        // the pdo mock should call prepare and return a pdostatement mock
        $this->pdoMock
            ->expects($this->once())
            ->method('prepare')
            ->with('UPDATE `game` (home_team_score, away_team_score)
            SET (:home_team_score, :away_team_score)
            WHERE game_id = :game_id')
            ->willReturn($this->pdoStatementMock);

        // execute method called once
        $this->pdoStatementMock
            ->expects($this->once())
            ->method('execute')
            ->with([
                ':game_id' => $event->getGameId(),
                ':home_team_score' => $event->getHomeTeamScore(),
                ':away_team_score' => $event->getAwayTeamScore()
            ]);

        $this->projection->projectGameScoreAdded($event);
    }
}
