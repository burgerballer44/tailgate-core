<?php

namespace Tailgate\Infrastructure\Persistence\ViewRepository\PDO;

use PDO;
use Tailgate\Domain\Model\Group\GroupId;
use Tailgate\Domain\Model\Group\FollowId;
use Tailgate\Domain\Model\Group\FollowView;
use Tailgate\Domain\Model\Group\FollowViewRepositoryInterface;
use Tailgate\Domain\Model\Team\TeamId;
use Tailgate\Domain\Model\Season\SeasonId;
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
        $stmt = $this->pdo->prepare('SELECT `follow`.follow_id, `follow`.group_id, `follow`.team_id, `follow`.season_id, `group`.name as groupName, `team`.designation, `team`.mascot, `season`.name as seasonName
            FROM `follow`
            JOIN `group` on `group`.group_id = `follow`.group_id
            JOIN `team` on `team`.team_id = `follow`.team_id
            JOIN `season` on `season`.season_id = `follow`.season_id
            WHERE `follow`.follow_id = :follow_id LIMIT 1');
        $stmt->execute([':follow_id' => (string) $id]);

        if (!$row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            throw new RepositoryException("Follow not found.");
        }

        return new FollowView(
            $row['group_id'],
            $row['follow_id'],
            $row['team_id'],
            $row['season_id'],
            $row['groupName'],
            $row['designation'],
            $row['mascot'],
            $row['seasonName']
        );
    }

    public function getAllByTeam(TeamId $id)
    {
        $stmt = $this->pdo->prepare('SELECT `follow`.follow_id, `follow`.group_id, `follow`.team_id, `follow`.season_id, `group`.name as groupName, `team`.designation, `team`.mascot, `season`.name as seasonName
            FROM `follow`
            JOIN `group` on `group`.group_id = `follow`.group_id
            JOIN `team` on `team`.team_id = `follow`.team_id
            JOIN `season` on `season`.season_id = `follow`.season_id
            WHERE `follow`.team_id = :team_id');
        $stmt->execute([':team_id' => (string) $id]);

        $follows = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $follows[] = new FollowView(
                $row['group_id'],
                $row['follow_id'],
                $row['team_id'],
                $row['season_id'],
                $row['groupName'],
                $row['designation'],
                $row['mascot'],
                $row['seasonName']
            );
        }

        return $follows;
    }

    public function getAllBySeason(SeasonId $id)
    {
        $stmt = $this->pdo->prepare('SELECT `follow`.follow_id, `follow`.group_id, `follow`.team_id, `follow`.season_id, `group`.name as groupName, `team`.designation, `team`.mascot, `season`.name as seasonName
            FROM `follow`
            JOIN `group` on `group`.group_id = `follow`.group_id
            JOIN `team` on `team`.team_id = `follow`.team_id
            JOIN `season` on `season`.season_id = `follow`.season_id
            WHERE `follow`.season_id = :season_id');
        $stmt->execute([':season_id' => (string) $id]);

        $follows = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $follows[] = new FollowView(
                $row['group_id'],
                $row['follow_id'],
                $row['team_id'],
                $row['season_id'],
                $row['groupName'],
                $row['designation'],
                $row['mascot'],
                $row['seasonName']
            );
        }

        return $follows;
    }

    public function getByGroup(GroupId $id)
    {
        $stmt = $this->pdo->prepare('SELECT `follow`.follow_id, `follow`.group_id, `follow`.team_id, `follow`.season_id, `group`.name as groupName, `team`.designation, `team`.mascot, `season`.name as seasonName
            FROM `follow`
            JOIN `group` on `group`.group_id = `follow`.group_id
            JOIN `team` on `team`.team_id = `follow`.team_id
            JOIN `season` on `season`.season_id = `follow`.season_id
            WHERE `follow`.group_id = :group_id
            LIMIT 1');
        $stmt->execute([':group_id' => (string) $id]);

        if (!$row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            return false;
        }

        return new FollowView(
            $row['group_id'],
            $row['follow_id'],
            $row['team_id'],
            $row['season_id'],
            $row['groupName'],
            $row['designation'],
            $row['mascot'],
            $row['seasonName']
        );
    }
}
