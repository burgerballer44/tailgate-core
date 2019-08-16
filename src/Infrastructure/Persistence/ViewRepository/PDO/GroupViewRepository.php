<?php

namespace Tailgate\Infrastructure\Persistence\ViewRepository\PDO;

use PDO;
use Tailgate\Domain\Model\Group\GroupId;
use Tailgate\Domain\Model\Group\GroupView;
use Tailgate\Domain\Model\Group\GroupViewRepositoryInterface;
use Tailgate\Infrastructure\Persistence\ViewRepository\RepositoryException;

class GroupViewRepository implements GroupViewRepositoryInterface
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function get(GroupId $id)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM `group` WHERE group_id = :group_id LIMIT 1');
        $stmt->execute([':group_id' => (string) $id]);

        if (!$row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            throw new RepositoryException("Group not found.");
        }

        return new GroupView(
            $row['group_id'],
            $row['name'],
            $row['owner_id']
        );
    }

    public function all()
    {
        $stmt = $this->pdo->prepare('SELECT * FROM `group`');
        $stmt->execute();

        $groups = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $groups[] = new GroupView(
                $row['group_id'],
                $row['name'],
                $row['owner_id']
            );
        }

        return $groups;
    }
}
