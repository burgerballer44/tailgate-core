<?php

namespace Tailgate\Infrastructure\Persistence\ViewRepository\PDO;

use PDO;
use Tailgate\Domain\Model\Group\GroupId;
use Tailgate\Domain\Model\Group\FollowView;
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

    public function get(GroupId $groupId)
    {
        $stmt = $this->pdo->prepare('SELECT `group`.group_id, `group`.name, `group`.invite_code, `group`.owner_id
            FROM `group`
            WHERE `group`.group_id = :group_id
            LIMIT 1');
        $stmt->execute([':group_id' => (string) $groupId]);

        if (!$row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            throw new RepositoryException("Group not found.");
        }

        return $this->createGroupView($row);
    }

    public function getByUser(UserId $userId, GroupId $groupId)
    {
        $stmt = $this->pdo->prepare('SELECT `group`.group_id, `group`.name, `group`.invite_code, `group`.owner_id
            FROM `group`
            JOIN `member` on `member`.group_id = `group`.group_id
            WHERE `member`.user_id = :user_id
            AND `group`.group_id = :group_id
            LIMIT 1');
        $stmt->execute([
            ':user_id' => (string) $userId,
            ':group_id' => (string) $groupId
        ]);

        if (!$row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            throw new RepositoryException("Group not found.");
        }

        return $this->createGroupView($row);
    }

    public function all()
    {
        $stmt = $this->pdo->prepare('SELECT `group`.group_id, `group`.name, `group`.invite_code, `group`.owner_id
            FROM `group`');
        $stmt->execute();

        $groups = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $groups[] = $this->createGroupView($row);
        }

        return $groups;
    }

    public function allByUser(UserId $id)
    {
        $stmt = $this->pdo->prepare('SELECT `group`.group_id, `group`.name, `group`.invite_code, `group`.owner_id
            FROM `group`
            JOIN `member` on `member`.group_id = `group`.group_id
            WHERE `member`.user_id = :user_id');
        $stmt->execute([':user_id' => (string) $id]);

        $groups = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $groups[] = $this->createGroupView($row);
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

        $stmt = $this->pdo->prepare('SELECT DISTINCT `group`.group_id, `group`.name, `group`.invite_code, `group`.owner_id FROM `group`
            JOIN `member` on `member`.group_id = `group`.group_id' . $specification->toSql());
        $stmt->execute($params);

        $groups = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $groups[] = $this->createGroupView($row);
        }

        return $groups;
    }

    public function byInviteCode($inviteCode)
    {
        $stmt = $this->pdo->prepare('SELECT `group`.group_id, `group`.name, `group`.invite_code, `group`.owner_id
            FROM `group`
            WHERE `group`.invite_code = :invite_code
            LIMIT 1');
        $stmt->execute([':invite_code' => (string) $inviteCode]);

        if (!$row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            throw new RepositoryException("Group not found by invite code.");
        }

        return $this->createGroupView($row);
    }

    private function createGroupView($row)
    {
        return new GroupView(
            $row['group_id'],
            $row['name'],
            $row['invite_code'],
            $row['owner_id']
        );
    }
}
