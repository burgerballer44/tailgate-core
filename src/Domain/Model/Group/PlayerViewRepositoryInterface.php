<?php

namespace Tailgate\Domain\Model\Group;

interface PlayerViewRepositoryInterface
{
    public function get(PlayerId $id);

    public function getAllByGroup(GroupId $id);

    public function byUsername($username);
}
