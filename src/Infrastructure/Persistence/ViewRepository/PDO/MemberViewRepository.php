<?php

namespace Tailgate\Infrastructure\Persistence\ViewRepository\PDO;

use PDO;
use Tailgate\Domain\Model\Group\GroupId;
use Tailgate\Domain\Model\Group\MemberId;
use Tailgate\Domain\Model\Group\MemberView;
use Tailgate\Domain\Model\Group\MemberViewRepositoryInterface;

class MemberViewRepository implements MemberViewRepositoryInterface
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function get(MemberId $id)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM `member` WHERE member_id = :member_id LIMIT 1');
        $stmt->execute([':member_id' => (string) $id]);

        if (!$row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            throw new \Exception("Member not found.");
        }

        return new MemberView(
            $row['member_id'],
            $row['group_id'],
            $row['user_id'],
            $row['role']
        );
    }

    public function getAllByGroup(GroupId $id)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM `member` WHERE group_id = :group_id');
        $stmt->execute([':group_id' => (string) $id]);

        $members = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $members[] = new MemberView(
                $row['member_id'],
                $row['group_id'],
                $row['user_id'],
                $row['role']
            );
        }

        return $members;
    }
}
