<?php

namespace Tailgate\Infrastructure\Persistence\ViewRepository\PDO;

use PDO;
use Tailgate\Domain\Model\Season\SeasonId;
use Tailgate\Domain\Model\Season\GameId;
use Tailgate\Domain\Model\Season\GameView;
use Tailgate\Domain\Model\Season\GameViewRepositoryInterface;
use Tailgate\Infrastructure\Persistence\ViewRepository\RepositoryException;

class GameViewRepository implements GameViewRepositoryInterface
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function get(GameId $id)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM `game` WHERE game_id = :game_id LIMIT 1');
        $stmt->execute([':game_id' => (string) $id]);

        if (!$row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            throw new RepositoryException("Game not found.");
        }

        return new GameView(
            $row['season_id'],
            $row['game_id'],
            $row['home_team_id'],
            $row['away_team_id'],
            $row['home_team_score'],
            $row['away_team_score'],
            $row['start_date']
        );
    }

    public function getAllBySeason(SeasonId $id)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM `game` WHERE season_id = :season_id');
        $stmt->execute([':season_id' => (string) $id]);

        $games = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $games[] = new GameView(
                $row['season_id'],
                $row['game_id'],
                $row['home_team_id'],
                $row['away_team_id'],
                $row['home_team_score'],
                $row['away_team_score'],
                $row['start_date']
            );
        }

        return $games;
    }
}
