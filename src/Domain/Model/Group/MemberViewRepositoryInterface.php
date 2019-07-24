<?php

namespace Tailgate\Domain\Model\Group;

use Tailgate\Domain\Model\User\UserId;
use Tailgate\Domain\Model\Group\GroupId;

interface MeMberViewRepositoryInterface
{
    public function get(GroupId $id, UserId $userId);
    public function all(GroupId $id);
}