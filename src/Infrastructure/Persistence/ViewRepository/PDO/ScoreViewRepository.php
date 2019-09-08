<?php

namespace Tailgate\Infrastructure\Persistence\ViewRepository\PDO;

use PDO;
use Tailgate\Domain\Model\Group\GroupId;
use Tailgate\Domain\Model\Group\PlayerId;
use Tailgate\Domain\Model\Group\ScoreId;
use Tailgate\Domain\Model\Season\GameId;
use Tailgate\Domain\Model\Group\ScoreView;
use Tailgate\Domain\Model\Group\ScoreViewRepositoryInterface;
use Tailgate\Infrastructure\Persistence\ViewRepository\RepositoryException;

class ScoreViewRepository implements ScoreViewRepositoryInterface
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function get(ScoreId $id)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM `score` WHERE score_id = :score_id LIMIT 1');
        $stmt->execute([':score_id' => (string) $id]);

        if (!$row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            throw new RepositoryException("Score not found.");
        }

        return new ScoreView(
            $row['score_id'],
            $row['group_id'],
            $row['player_id'],
            $row['game_id'],
            $row['home_team_prediction'],
            $row['away_team_prediction']
        );
    }

    public function getAllByGroup(GroupId $id)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM `score` WHERE group_id = :group_id');
        $stmt->execute([':group_id' => (string) $id]);

        $scores = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $scores[] = new ScoreView(
                $row['score_id'],
                $row['group_id'],
                $row['player_id'],
                $row['game_id'],
                $row['home_team_prediction'],
                $row['away_team_prediction']
            );
        }

        return $scores;
    }

    public function getAllByGroupPlayer(GroupId $id, PlayerId $playerId)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM `score` WHERE group_id = :group_id AND player_id = :player_id');
        $stmt->execute([':group_id' => (string) $id, ':player_id' => (string) $playerId]);

        $scores = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $scores[] = new ScoreView(
                $row['score_id'],
                $row['group_id'],
                $row['player_id'],
                $row['game_id'],
                $row['home_team_prediction'],
                $row['away_team_prediction']
            );
        }

        return $scores;
    }

    public function getAllByGroupGame(GroupId $id, GameId $game)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM `score` WHERE group_id = :group_id AND game_id = :game_id');
        $stmt->execute([':group_id' => (string) $id, ':game_id' => (string) $game]);

        $scores = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $scores[] = new ScoreView(
                $row['score_id'],
                $row['group_id'],
                $row['player_id'],
                $row['game_id'],
                $row['home_team_prediction'],
                $row['away_team_prediction']
            );
        }

        return $scores;
    }
}
