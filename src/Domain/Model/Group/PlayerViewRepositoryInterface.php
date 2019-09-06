<?php

namespace Tailgate\Domain\Model\Group;

use Tailgate\Domain\Model\Group\GroupId;
use Tailgate\Domain\Model\Group\PlayerId;

interface PlayerViewRepositoryInterface
{
    public function get(PlayerId $id);
    public function getAllByGroup(GroupId $id);
    public function byUsername($username);
}
