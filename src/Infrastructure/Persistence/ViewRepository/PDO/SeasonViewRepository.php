<?php

namespace Tailgate\Infrastructure\Persistence\ViewRepository\PDO;

use PDO;
use Tailgate\Domain\Model\Season\SeasonId;
use Tailgate\Domain\Model\Season\SeasonView;
use Tailgate\Domain\Model\Season\SeasonViewRepositoryInterface;

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
            throw new \Exception("Season not found.");
        }

        return new SeasonView(
            $row['season_id'],
            $row['sport'],
            $row['type'],
            $row['name'],
            $row['season_start'],
            $row['season_end']
        );
    }

    public function all()
    {
        $stmt = $this->pdo->prepare('SELECT * FROM `season`');
        $stmt->execute();

        $seasons = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $seasons[] =  new SeasonView(
                $row['season_id'],
                $row['sport'],
                $row['type'],
                $row['name'],
                $row['season_start'],
                $row['season_end']
            );
        }

        return $seasons;
    }
}
