<?php

namespace Tailgate\Domain\Model\Group;

use Tailgate\Domain\Model\Group\GroupId;
use Tailgate\Domain\Model\User\UserId;

interface GroupViewRepositoryInterface
{
    public function query(UserId $id, string $name);
    public function get(GroupId $id);
    public function all(UserId $id);
}
