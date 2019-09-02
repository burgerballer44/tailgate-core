<?php

namespace Tailgate\Domain\Model\Group;

use Tailgate\Domain\Model\Group\PlayerId;

interface PlayerViewRepositoryInterface
{
    public function get(PlayerId $id);
    public function all();
    public function byUsername($username);
}
