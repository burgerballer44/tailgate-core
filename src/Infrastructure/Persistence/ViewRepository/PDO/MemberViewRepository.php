<?php

namespace Tailgate\Infrastructure\Persistence\ViewRepository\PDO;

use PDO;
use Tailgate\Domain\Model\User\UserId;
use Tailgate\Domain\Model\Group\GroupId;
use Tailgate\Domain\Model\Group\MemberView;
use Tailgate\Domain\Model\Group\MemberViewRepositoryInterface;

class MemberViewRepository implements MemberViewRepositoryInterface
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function get(GroupId $id, UserId $userId)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM `member` WHERE group_id = :group_id AND user_id = :user_id');
        $stmt->execute([':group_id' => (string) $id, ':user_id' => (string) $userId]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return new MemberView(
            $row['member_id'],
            $row['group_id'],
            $row['user_id'],
            $row['role']
        );
    }

    public function all(GroupId $id)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM `member` WHERE group_id = :group_id');
        $stmt->execute();

        $groups = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $groups[] = new MemberView(
                $row['member_id'],
                $row['group_id'],
                $row['user_id'],
                $row['role']
            );
        }

        return $groups;
    }
}