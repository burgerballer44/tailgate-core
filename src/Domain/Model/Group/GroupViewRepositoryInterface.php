<?php

namespace Tailgate\Domain\Model\Group;

use Tailgate\Domain\Model\User\UserId;

interface GroupViewRepositoryInterface
{
    public function query(UserId $id, string $name);

    public function get(GroupId $groupId);

    public function all();

    public function getByUser(UserId $userId, GroupId $groupId);

    public function allByUser(UserId $id);

    public function byInviteCode($inviteCode);
}
