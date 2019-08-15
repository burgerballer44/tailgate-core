<?php

namespace Tailgate\Domain\Model\Group;

use Tailgate\Domain\Model\Group\GroupId;
use Tailgate\Domain\Model\Group\MemberId;

interface MemberViewRepositoryInterface
{
    public function get(MemberId $id);
    public function getAllByGroup(GroupId $id);
}
