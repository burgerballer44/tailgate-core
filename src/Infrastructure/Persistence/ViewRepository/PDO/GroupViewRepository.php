<?php

namespace Tailgate\Infrastructure\Persistence\ViewRepository\PDO;

use PDO;
use Tailgate\Domain\Model\Group\GroupId;
use Tailgate\Domain\Model\Group\GroupView;
use Tailgate\Domain\Model\Group\GroupViewRepositoryInterface;
use Tailgate\Domain\Model\User\UserId;
use Tailgate\Infrastructure\Persistence\ViewRepository\PDO\Specification\AndSpecification;
use Tailgate\Infrastructure\Persistence\ViewRepository\PDO\Specification\NameSpecification;
use Tailgate\Infrastructure\Persistence\ViewRepository\PDO\Specification\UserSpecification;
use Tailgate\Infrastructure\Persistence\ViewRepository\PDO\Specification\WhereSpecification;
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

    public function query(UserId $userId, string $name)
    {
        $params = [];
        $specification = new WhereSpecification();

        if ((string) $userId) {
            $specification = new AndSpecification($specification);
            $specification = new UserSpecification($specification);
            $params[':user_id'] = (string) $userId;
        }

        if ($name) {
            $specification = new AndSpecification($specification);
            $specification = new NameSpecification($specification);
            $params[':name'] = "%{$name}%";
        }

        $stmt = $this->pdo->prepare('SELECT * FROM `group`
            JOIN `member` on `member`.group_id = `group`.group_id' . $specification->toSql());
        $stmt->execute($params);

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

    public function getAllByUser(UserId $id)
    {
        $stmt = $this->pdo->prepare('SELECT group.group_id, group.name, group.owner_id FROM `group`
            JOIN `member` on `member`.group_id = `group`.group_id
            WHERE `member`.user_id = :user_id');
        $stmt->execute([':user_id' => (string) $id]);

        $members = [];

       while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
           $groups[] = new GroupView(
               $row['group_id'],
               $row['name'],
               $row['owner_id']
           );
       }

        return $members;
    }
}
