<?php

namespace Tailgate\Infrastructure\Persistence\ViewRepository\PDO;

use PDO;
use RuntimeException;
use Tailgate\Domain\Model\Season\SeasonId;
use Tailgate\Domain\Model\Team\TeamId;
use Tailgate\Domain\Model\Team\TeamView;
use Tailgate\Domain\Model\Team\TeamViewRepositoryInterface;

class TeamViewRepository implements TeamViewRepositoryInterface
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function get(TeamId $id)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM `team` WHERE team_id = :team_id LIMIT 1');
        $stmt->execute([':team_id' => (string) $id]);

        if (!$row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            throw new RuntimeException("Team not found.");
        }

        return $this->createTeamView($row);
    }

    public function allBySport($sport)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM `team` WHERE sport = :sport');
        $stmt->execute([':sport' => $sport]);

        $teams = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $teams[] = $this->createTeamView($row);
        }

        return $teams;
    }

    public function all()
    {
        $stmt = $this->pdo->prepare('SELECT * FROM `team`');
        $stmt->execute();

        $teams = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $teams[] = $this->createTeamView($row);
        }

        return $teams;
    }

    private function createTeamView($row)
    {
        return new TeamView(
            $row['team_id'],
            $row['designation'],
            $row['mascot'],
            $row['sport']
        );
    }
}
