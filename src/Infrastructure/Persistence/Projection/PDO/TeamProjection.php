<?php

namespace Tailgate\Infrastructure\Persistence\Projection\PDO;

use PDO;
use Tailgate\Domain\Model\Team\TeamAdded;
use Tailgate\Domain\Model\Team\TeamDeleted;
use Tailgate\Domain\Model\Team\TeamUpdated;
use Tailgate\Infrastructure\Persistence\Projection\AbstractProjection;
use Tailgate\Infrastructure\Persistence\Projection\TeamProjectionInterface;

class TeamProjection extends AbstractProjection implements TeamProjectionInterface
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function projectTeamAdded(TeamAdded $event)
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO `team` (team_id, designation, mascot, sport, created_at)
            VALUES (:team_id, :designation, :mascot, :sport, :created_at)'
        );

        $stmt->execute([
            ':team_id' => $event->getAggregateId(),
            ':designation' => $event->getDesignation(),
            ':mascot' => $event->getMascot(),
            ':sport' => $event->getSport(),
            ':created_at' => (new \DateTimeImmutable())->format(self::DATE_FORMAT),
        ]);
    }

    public function projectTeamUpdated(TeamUpdated $event)
    {
        $stmt = $this->pdo->prepare('UPDATE `team`
            SET designation = :designation, mascot = :mascot
            WHERE team_id = :team_id');

        $stmt->execute([
            ':team_id' => $event->getAggregateId(),
            ':designation' => $event->getDesignation(),
            ':mascot' => $event->getMascot(),
        ]);
    }

    public function projectTeamDeleted(TeamDeleted $event)
    {
        $stmt = $this->pdo->prepare('DELETE FROM `score` WHERE game_id IN
            (SELECT game_id FROM game WHERE home_team_id = :team_id OR away_team_id = :team_id)');
        $stmt->execute([':team_id' => $event->getAggregateId()]);

        $stmt = $this->pdo->prepare('DELETE FROM `game` WHERE home_team_id = :team_id OR away_team_id = :team_id');
        $stmt->execute([':team_id' => $event->getAggregateId()]);

        $stmt = $this->pdo->prepare('DELETE FROM `follow` WHERE team_id = :team_id');
        $stmt->execute([':team_id' => $event->getAggregateId()]);

        $stmt = $this->pdo->prepare('DELETE FROM `team` WHERE team_id = :team_id');
        $stmt->execute([':team_id' => $event->getAggregateId()]);
    }
}
