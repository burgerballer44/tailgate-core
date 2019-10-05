<?php

namespace Tailgate\Domain\Model\Group;

use Tailgate\Domain\Model\Group\GroupId;
use Tailgate\Domain\Model\User\UserId;

interface GroupViewRepositoryInterface
{
    public function get(GroupId $id);
    public function all();
    public function getAllByUser(UserId $id);
}
