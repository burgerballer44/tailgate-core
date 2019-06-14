<?php

namespace Tailgate\Infrastructure\Persistence\Projection\PDO;

use PDO;
use Tailgate\Domain\Model\Season\GameAdded;
use Tailgate\Domain\Model\Season\GameScoreAdded;
use Tailgate\Domain\Model\Season\SeasonCreated;
use Tailgate\Domain\Model\Season\SeasonProjectionInterface;
use Tailgate\Infrastructure\Persistence\Projection\AbstractProjection;

class PDOSeasonProjection extends AbstractProjection implements SeasonProjectionInterface
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function projectSeasonCreated(SeasonCreated $event)
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO season (season_id, sport, type, name, season_start, season_end, created_at)
            VALUES (:season_id, :sport, :type, :name, :season_start, :season_end, :created_at)'
        );

        $stmt->execute([
            ':season_id' => $event->getAggregateId(),
            ':sport' => $event->getSport(),
            ':type' => $event->getSeasonType(),
            ':name' => $event->getName(),
            ':season_start' => $event->getSeasonStart(),
            ':season_end' => $event->getSeasonEnd(),
            ':created_at' => (new \DateTimeImmutable())->format('Y-m-d H:i:s')
        ]);
    }

    public function projectGameAdded(GameAdded $event)
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO game (game_id, season_id, home_team_id, away_team_id, start_date, created_at)
            VALUES (:game_id, :season_id, :home_team_id, :away_team_id, :start_date, :created_at)'
        );

        $stmt->execute([
            ':season_id' => $event->getAggregateId(),
            ':game_id' => $event->getGameId(),
            ':home_team_id' => $event->getHomeTeamId(),
            ':away_team_id' => $event->getAwayTeamId(),
            ':start_date' => $event->getStartDate(),
            ':created_at' => (new \DateTimeImmutable())->format('Y-m-d H:i:s')
        ]);
    }

    public function projectGameScoreAdded(GameScoreAdded $event)
    {
        $stmt = $this->pdo->prepare(
            'UPDATE game (home_team_score, away_team_score)
            SET (:home_team_score, :away_team_score)
            WHERE game_id = :game_id'
        );

        $stmt->execute([
            ':game_id' => $event->getGameId(),
            ':home_team_score' => $event->getHomeTeamScore(),
            ':away_team_score' => $event->getAwayTeamScore()
        ]);
    }
}