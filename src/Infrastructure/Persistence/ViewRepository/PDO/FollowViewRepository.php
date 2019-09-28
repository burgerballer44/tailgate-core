<?php

namespace Tailgate\Infrastructure\Persistence\ViewRepository\PDO;

use PDO;
use Tailgate\Domain\Model\Team\TeamId;
use Tailgate\Domain\Model\Team\FollowId;
use Tailgate\Domain\Model\Team\FollowView;
use Tailgate\Domain\Model\Team\FollowViewRepositoryInterface;
use Tailgate\Infrastructure\Persistence\ViewRepository\RepositoryException;

class FollowViewRepository implements FollowViewRepositoryInterface
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function get(FollowId $id)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM `follow`
            JOIN `group` on `group`.group_id = `follow`.group_id
            JOIN `team` on `team`.team_id = `follow`.team_id
            WHERE `follow`.follow_id = :follow_id LIMIT 1');
        $stmt->execute([':follow_id' => (string) $id]);

        if (!$row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            throw new RepositoryException("Follow not found.");
        }

        return new FollowView(
            $row['team_id'],
            $row['follow_id'],
            $row['group_id'],
            $row['name'],
            $row['designation'],
            $row['mascot']
        );
    }

    public function getAllByTeam(TeamId $id)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM `follow`
            JOIN `group` on `group`.group_id = `follow`.group_id
            JOIN `team` on `team`.team_id = `follow`.team_id
            WHERE `follow`.team_id = :team_id');
        $stmt->execute([':team_id' => (string) $id]);

        $follows = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $follows[] = new FollowView(
                $row['team_id'],
                $row['follow_id'],
                $row['group_id'],
                $row['name'],
                $row['designation'],
                $row['mascot']
            );
        }

        return $follows;
    }
}
