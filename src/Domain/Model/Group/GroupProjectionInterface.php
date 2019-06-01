<?php

namespace Tailgate\Domain\Model\Group;

use Tailgate\Common\Projection\ProjectionInterface;

interface GroupProjectionInterface extends ProjectionInterface
{
    public function projectGroupCreated(GroupCreated $event);
    public function projectScoreSubmitted(ScoreSubmitted $event);
}