<?php

namespace Tailgate\Infrastructure\Persistence\Projection\PDO;

use PDO;
use Tailgate\Domain\Model\Team\TeamAdded;
use Tailgate\Domain\Model\Team\TeamFollowed;
use Tailgate\Domain\Model\Team\TeamUpdated;
use Tailgate\Domain\Model\Team\TeamDeleted;
use Tailgate\Domain\Model\Team\FollowDeleted;
use Tailgate\Domain\Model\Team\TeamProjectionInterface;
use Tailgate\Infrastructure\Persistence\Projection\AbstractProjection;

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
            'INSERT INTO `team` (team_id, designation, mascot, created_at)
            VALUES (:team_id, :designation, :mascot, :created_at)'
        );

        $stmt->execute([
            ':team_id' => $event->getAggregateId(),
            ':designation' => $event->getDesignation(),
            ':mascot' => $event->getMascot(),
            ':created_at' => (new \DateTimeImmutable())->format('Y-m-d H:i:s')
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
            ':created_at' => (new \DateTimeImmutable())->format('Y-m-d H:i:s')
        ]);
    }

    public function projectTeamFollowed(TeamFollowed $event)
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO `follow` (follow_id, team_id, group_id)
            VALUES (:follow_id, :team_id, :group_id)'
        );

        $stmt->execute([
            ':follow_id' => $event->getFollowId(),
            ':group_id' => $event->getGroupId(),
            ':team_id' => $event->getAggregateId(),
            ':created_at' => (new \DateTimeImmutable())->format('Y-m-d H:i:s')
        ]);
    }

    public function projectFollowDeleted(FollowDeleted $event)
    {
        $stmt = $this->pdo->prepare('DELETE FROM `follow` WHERE follow_id = :follow_id');

        $stmt->execute([':follow_id' => $event->getFollowId()]);
    }

    public function projectTeamDeleted(TeamDeleted $event)
    {
        $stmt = $this->pdo->prepare('DELETE FROM `team` WHERE team_id = :team_id');

        $stmt->execute([':team_id' => $event->getAggregateId()]);
    }
}
