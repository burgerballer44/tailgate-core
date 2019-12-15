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
        $stmt = $this->pdo->prepare('SELECT s.score_id, s.group_id, s.player_id, s.game_id, s.home_team_prediction, s.away_team_prediction, g.home_team_id, g.away_team_id, hot.designation as home_designation, hot.mascot as home_mascot, awt.designation as away_designation, awt.mascot as away_mascot
            FROM `score` s
            JOIN `game` g on g.game_id = s.game_id
            JOIN `team` hot on hot.team_id = g.home_team_id
            JOIN `team` awt on awt.team_id = g.away_team_id
            WHERE s.score_id = :score_id LIMIT 1');
        $stmt->execute([':score_id' => (string) $id]);

        if (!$row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            throw new RepositoryException("Score not found.");
        }

        return $this->createScoreView($row);
    }

    public function getAllByGroup(GroupId $id)
    {
        $stmt = $this->pdo->prepare('SELECT s.score_id, s.group_id, s.player_id, s.game_id, s.home_team_prediction, s.away_team_prediction, g.home_team_id, g.away_team_id, hot.designation as home_designation, hot.mascot as home_mascot, awt.designation as away_designation, awt.mascot as away_mascot
            FROM `score` s
            JOIN `game` g on g.game_id = s.game_id
            JOIN `team` hot on hot.team_id = g.home_team_id
            JOIN `team` awt on awt.team_id = g.away_team_id
            WHERE s.group_id = :group_id');
        $stmt->execute([':group_id' => (string) $id]);

        $scores = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $scores[] = $this->createScoreView($row);
        }

        return $scores;
    }

    public function getAllByGroupPlayer(GroupId $id, PlayerId $playerId)
    {
        $stmt = $this->pdo->prepare('SELECT s.score_id, s.group_id, s.player_id, s.game_id, s.home_team_prediction, s.away_team_prediction, g.home_team_id, g.away_team_id, hot.designation as home_designation, hot.mascot as home_mascot, awt.designation as away_designation, awt.mascot as away_mascot
            FROM `score` s
            JOIN `game` g on g.game_id = s.game_id
            JOIN `team` hot on hot.team_id = g.home_team_id
            JOIN `team` awt on awt.team_id = g.away_team_id
            WHERE s.group_id = :group_id AND s.player_id = :player_id');
        $stmt->execute([':group_id' => (string) $id, ':player_id' => (string) $playerId]);

        $scores = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $scores[] = $this->createScoreView($row);
        }

        return $scores;
    }

    public function getAllByGroupGame(GroupId $id, GameId $game)
    {
        $stmt = $this->pdo->prepare('SELECT s.score_id, s.group_id, s.player_id, s.game_id, s.home_team_prediction, s.away_team_prediction, g.home_team_id, g.away_team_id, hot.designation as home_designation, hot.mascot as home_mascot, awt.designation as away_designation, awt.mascot as away_mascot
            FROM `score` s
            JOIN `game` g on g.game_id = s.game_id
            JOIN `team` hot on hot.team_id = g.home_team_id
            JOIN `team` awt on awt.team_id = g.away_team_id
            WHERE s.group_id = :group_id AND s.game_id = :game_id');
        $stmt->execute([':group_id' => (string) $id, ':game_id' => (string) $game]);

        $scores = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $scores[] = $this->createScoreView($row);
        }

        return $scores;
    }

    private function createScoreView($row)
    {
        return new ScoreView(
            $row['score_id'],
            $row['group_id'],
            $row['player_id'],
            $row['game_id'],
            $row['home_team_prediction'],
            $row['away_team_prediction'],
            $row['home_team_id'],
            $row['away_team_id'],
            $row['home_designation'],
            $row['home_mascot'],
            $row['away_designation'],
            $row['away_mascot']
        );
    }
}
