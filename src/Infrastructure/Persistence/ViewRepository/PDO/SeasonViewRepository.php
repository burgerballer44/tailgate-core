<?php

namespace Tailgate\Infrastructure\Persistence\ViewRepository\PDO;

use PDO;
use Tailgate\Domain\Model\Season\SeasonId;
use Tailgate\Domain\Model\Season\SeasonView;
use Tailgate\Domain\Model\Season\SeasonViewRepositoryInterface;
use Tailgate\Infrastructure\Persistence\ViewRepository\RepositoryException;

class SeasonViewRepository implements SeasonViewRepositoryInterface
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function get(SeasonId $id)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM `season` WHERE season_id = :season_id LIMIT 1');
        $stmt->execute([':season_id' => (string) $id]);

        if (!$row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            throw new RepositoryException("Season not found.");
        }

        return $this->createSeasonView($row);
    }

    public function allBySport($sport)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM `season` WHERE sport_id = :sport_id LIMIT 1');
        $stmt->execute([':sport_id' => (string) $sport]);

        if (!$row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            throw new RepositoryException("Season not found by sport.");
        }

        return $this->createSeasonView($row);
    }

    public function all()
    {
        $stmt = $this->pdo->prepare('SELECT * FROM `season`');
        $stmt->execute();

        $seasons = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $seasons[] =  $this->createSeasonView($row);;
        }

        return $seasons;
    }

    private function createSeasonView($row)
    {
        $seasonStartDateTime = \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $row['season_start']);
        $seasonStart = $seasonStartDateTime instanceof \DateTimeImmutable ? $seasonStartDateTime->format('Y-m-d') : $row['season_start'];
        $seasonEndDateTime = \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $row['season_end']);
        $seasonEnd = $seasonEndDateTime instanceof \DateTimeImmutable ? $seasonEndDateTime->format('Y-m-d') : $row['season_end'];

        return new SeasonView(
            $row['season_id'],
            $row['sport'],
            $row['type'],
            $row['name'],
            $seasonStart,
            $seasonEnd
        );
    }
}
