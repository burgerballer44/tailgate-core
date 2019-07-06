<?php

namespace Tailgate\Infrastructure\Persistence\ViewRepository\PDO;

use PDO;
use Tailgate\Domain\Model\Team\TeamId;
use Tailgate\Domain\Model\Team\TeamView;
use Tailgate\Domain\Model\Team\TeamViewRepositoryInterface;

class PDOTeamViewRepository implements TeamViewRepositoryInterface
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function get(TeamId $id)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM `team` WHERE team_id = :team_id');
        $stmt->execute([':team_id' => (string) $id]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return new TeamView(
            $row['team_id'],
            $row['designation'],
            $row['mascot']
        );
    }

    public function all()
    {
        $stmt = $this->pdo->prepare('SELECT * FROM `team`');
        $stmt->execute();

        $teams = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $teams[] = new TeamView(
                $row['team_id'],
                $row['designation'],
                $row['mascot']
            );
        }

        return $teams;
    }
}