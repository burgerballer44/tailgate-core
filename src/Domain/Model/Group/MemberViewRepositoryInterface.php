<?php

namespace Tailgate\Domain\Model\Group;

use Tailgate\Domain\Model\User\UserId;

interface MemberViewRepositoryInterface
{
    public function get(MemberId $id);

    public function getAllByGroup(GroupId $id);

    public function getAllByUser(UserId $id);
}
