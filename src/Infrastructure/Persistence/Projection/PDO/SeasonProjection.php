<?php

namespace Tailgate\Infrastructure\Persistence\Projection\PDO;

use PDO;
use Tailgate\Domain\Model\Season\GameAdded;
use Tailgate\Domain\Model\Season\GameScoreUpdated;
use Tailgate\Domain\Model\Season\SeasonCreated;
use Tailgate\Domain\Model\Season\SeasonDeleted;
use Tailgate\Domain\Model\Season\SeasonUpdated;
use Tailgate\Domain\Model\Season\GameDeleted;
use Tailgate\Domain\Model\Season\SeasonProjectionInterface;
use Tailgate\Infrastructure\Persistence\Projection\AbstractProjection;

class SeasonProjection extends AbstractProjection implements SeasonProjectionInterface
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function projectSeasonCreated(SeasonCreated $event)
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO `season` (season_id, sport, type, name, season_start, season_end, created_at)
            VALUES (:season_id, :sport, :type, :name, :season_start, :season_end, :created_at)'
        );

        $stmt->execute([
            ':season_id' => $event->getAggregateId(),
            ':sport' => $event->getSport(),
            ':type' => $event->getSeasonType(),
            ':name' => $event->getName(),
            ':season_start' => $event->getSeasonStart()->format('Y-m-d H:i:s'),
            ':season_end' => $event->getSeasonEnd()->format('Y-m-d H:i:s'),
            ':created_at' => (new \DateTimeImmutable())->format('Y-m-d H:i:s')
        ]);
    }

    public function projectGameAdded(GameAdded $event)
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO `game` (game_id, season_id, home_team_id, away_team_id, start_date, created_at)
            VALUES (:game_id, :season_id, :home_team_id, :away_team_id, :start_date, :created_at)'
        );

        $stmt->execute([
            ':season_id' => $event->getAggregateId(),
            ':game_id' => $event->getGameId(),
            ':home_team_id' => $event->getHomeTeamId(),
            ':away_team_id' => $event->getAwayTeamId(),
            ':start_date' => $event->getStartDate()->format('Y-m-d H:i:s'),
            ':created_at' => (new \DateTimeImmutable())->format('Y-m-d H:i:s')
        ]);
    }

    public function projectGameScoreUpdated(GameScoreUpdated $event)
    {
        $stmt = $this->pdo->prepare(
            'UPDATE `game` SET home_team_score = :home_team_score, away_team_score = :away_team_score
            WHERE game_id = :game_id'
        );

        $stmt->execute([
            ':game_id' => $event->getGameId(),
            ':home_team_score' => $event->getHomeTeamScore(),
            ':away_team_score' => $event->getAwayTeamScore()
        ]);
    }

    public function projectGameDeleted(GameDeleted $event)
    {
        $stmt = $this->pdo->prepare('DELETE FROM `game` WHERE game_id = :game_id');

        $stmt->execute([':game_id' => $event->getGameId()]);
    }

    public function projectSeasonDeleted(SeasonDeleted $event)
    {
        $stmt = $this->pdo->prepare('DELETE FROM `game` WHERE season_id = :season_id');
        $stmt->execute([':season_id' => $event->getAggregateId()]);

        $stmt = $this->pdo->prepare('DELETE FROM `season` WHERE season_id = :season_id');
        $stmt->execute([':season_id' => $event->getAggregateId()]);
    }

    public function projectSeasonUpdated(SeasonUpdated $event)
    {
        $stmt = $this->pdo->prepare(
            'UPDATE `season` SET sport = :sport, type = :type, name = :name, season_start = :season_start, season_end = :season_end)
            WHERE season_id = :season_id'
        );

        $stmt->execute([
            ':season_id' => $event->getAggregateId(),
            ':sport' => $event->getSport(),
            ':type' => $event->getSeasonType(),
            ':name' => $event->getName(),
            ':season_start' => $event->getSeasonStart()->format('Y-m-d H:i:s'),
            ':season_end' => $event->getSeasonEnd()->format('Y-m-d H:i:s')
        ]);
    }
}
