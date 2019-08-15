<?php

namespace Tailgate\Domain\Model\Group;

use Tailgate\Domain\Model\Group\GroupId;

interface GroupViewRepositoryInterface
{
    public function get(GroupId $id);
    public function all();
}
