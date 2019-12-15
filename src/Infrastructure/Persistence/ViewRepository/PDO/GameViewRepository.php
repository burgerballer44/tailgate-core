<?php

namespace Tailgate\Infrastructure\Persistence\ViewRepository\PDO;

use PDO;
use Tailgate\Domain\Model\Season\GameId;
use Tailgate\Domain\Model\Season\GameView;
use Tailgate\Domain\Model\Season\GameViewRepositoryInterface;
use Tailgate\Domain\Model\Season\SeasonId;
use Tailgate\Domain\Model\Team\TeamId;
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
        $stmt = $this->pdo->prepare('SELECT g.season_id, g.game_id, g.home_team_id, g.away_team_id, g.home_team_score, g.away_team_score, g.start_date, hot.designation as home_designation, hot.mascot as home_mascot, awt.designation as away_designation, awt.mascot as away_mascot
            FROM `game` g 
            JOIN `team` hot on hot.team_id = g.home_team_id
            JOIN `team` awt on awt.team_id = g.away_team_id
            WHERE g.game_id = :game_id LIMIT 1');
        $stmt->execute([':game_id' => (string) $id]);

        if (!$row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            throw new RepositoryException("Game not found.");
        }

        return $this->createGameView($row);
    }

    public function getAllBySeason(SeasonId $id)
    {
        $stmt = $this->pdo->prepare('SELECT g.season_id, g.game_id, g.home_team_id, g.away_team_id, g.home_team_score, g.away_team_score, g.start_date, hot.designation as home_designation, hot.mascot as home_mascot, awt.designation as away_designation, awt.mascot as away_mascot
            FROM `game` g 
            JOIN `team` hot on hot.team_id = g.home_team_id
            JOIN `team` awt on awt.team_id = g.away_team_id
            WHERE g.season_id = :season_id');
        $stmt->execute([':season_id' => (string) $id]);

        $games = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $games[] = $this->createGameView($row);
        }

        return $games;
    }

    public function getAllByTeam(TeamId $id)
    {
        $stmt = $this->pdo->prepare('SELECT g.season_id, g.game_id, g.home_team_id, g.away_team_id, g.home_team_score, g.away_team_score, g.start_date, hot.designation as home_designation, hot.mascot as home_mascot, awt.designation as away_designation, awt.mascot as away_mascot
            FROM `game` g
            JOIN `team` hot on hot.team_id = g.home_team_id
            JOIN `team` awt on awt.team_id = g.away_team_id
            WHERE g.home_team_id = :team_id OR g.away_team_id = :team_id');
        $stmt->execute([':team_id' => (string) $id]);

        $games = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $games[] = $this->createGameView($row);
        }

        return $games;
    }

    private function createGameView($row)
    {
        $startDateDateTime = \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $row['start_date']);
        $startDate = $startDateDateTime instanceof \DateTimeImmutable ? $startDateDateTime->format('M j, Y (D) / g:i A') : $row['start_date'];

        return new GameView(
            $row['season_id'],
            $row['game_id'],
            $row['home_team_id'],
            $row['away_team_id'],
            $row['home_team_score'],
            $row['away_team_score'],
            $startDate,
            $row['home_designation'],
            $row['home_mascot'],
            $row['away_designation'],
            $row['away_mascot']
        );
    }
}
