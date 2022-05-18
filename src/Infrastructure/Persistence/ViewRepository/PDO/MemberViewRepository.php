<?php

namespace Tailgate\Infrastructure\Persistence\ViewRepository\PDO;

use PDO;
use RuntimeException;
use Tailgate\Domain\Model\Group\GroupId;
use Tailgate\Domain\Model\Group\MemberId;
use Tailgate\Domain\Model\Group\MemberView;
use Tailgate\Domain\Model\Group\MemberViewRepositoryInterface;
use Tailgate\Domain\Model\User\UserId;

class MemberViewRepository implements MemberViewRepositoryInterface
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function get(MemberId $id)
    {
        $stmt = $this->pdo->prepare('SELECT `member`.member_id, `member`.group_id, `member`.user_id, `member`.role, `member`.allow_multiple, `user`.email
            FROM `member`
            JOIN `user` on `user`.user_id = `member`.user_id
            WHERE `member`.member_id = :member_id LIMIT 1');
        $stmt->execute([':member_id' => (string) $id]);

        if (! $row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            throw new RuntimeException("Member not found.");
        }

        return $this->createMemberView($row);
    }

    public function getAllByGroup(GroupId $id)
    {
        $stmt = $this->pdo->prepare('SELECT `member`.member_id, `member`.group_id, `member`.user_id, `member`.role, `member`.allow_multiple, `user`.email
            FROM `member`
            JOIN `user` on `user`.user_id = `member`.user_id
            WHERE `member`.group_id = :group_id');
        $stmt->execute([':group_id' => (string) $id]);

        $members = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $members[] = $this->createMemberView($row);
        }

        return $members;
    }

    public function getAllByUser(UserId $id)
    {
        $stmt = $this->pdo->prepare('SELECT `member`.member_id, `member`.group_id, `member`.user_id, `member`.role, `member`.allow_multiple, `user`.email
            FROM `member`
            JOIN `user` on `user`.user_id = `member`.user_id
            WHERE `member`.user_id = :user_id');
        $stmt->execute([':user_id' => (string) $id]);

        $members = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $members[] = $this->createMemberView($row);
        }

        return $members;
    }

    private function createMemberView($row)
    {
        return new MemberView(
            $row['member_id'],
            $row['group_id'],
            $row['user_id'],
            $row['role'],
            $row['allow_multiple'],
            $row['email']
        );
    }
}
