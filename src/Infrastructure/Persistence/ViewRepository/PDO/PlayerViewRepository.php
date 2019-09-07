<?php

namespace Tailgate\Infrastructure\Persistence\ViewRepository\PDO;

use PDO;
use Tailgate\Domain\Model\Group\GroupId;
use Tailgate\Domain\Model\Group\MemberId;
use Tailgate\Domain\Model\Group\PlayerId;
use Tailgate\Domain\Model\Group\PlayerView;
use Tailgate\Domain\Model\Group\PlayerViewRepositoryInterface;
use Tailgate\Infrastructure\Persistence\ViewRepository\RepositoryException;

class PlayerViewRepository implements PlayerViewRepositoryInterface
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function get(PlayerId $id)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM `player` WHERE player_id = :player_id LIMIT 1');
        $stmt->execute([':player_id' => (string) $id]);

        if (!$row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            throw new RepositoryException("Player not found.");
        }

        return new PlayerView(
            $row['player_id'],
            $row['member_id'],
            $row['group_id'],
            $row['username']
        );
    }

    public function getAllByGroup(GroupId $id)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM `player` WHERE group_id = :group_id');
        $stmt->execute([':group_id' => (string) $id]);

        $players = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $players[] = new PlayerView(
                $row['player_id'],
                $row['member_id'],
                $row['group_id'],
                $row['username']
            );
        }

        return $players;
    }

    public function byUsername($username)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM `player` WHERE username = :username');
        $stmt->execute([':username' => (string) $username]);

        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            return new PlayerView(
                $row['player_id'],
                $row['username'],
                $row['status'],
                $row['role']
            );
        }

        return false;
    }
}