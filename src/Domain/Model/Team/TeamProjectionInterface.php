<?php

namespace Tailgate\Domain\Model\Team;

use Tailgate\Common\Projection\ProjectionInterface;

interface TeamProjectionInterface extends ProjectionInterface
{
    public function projectTeamAdded(TeamAdded $event);
    public function projectTeamFollowed(TeamFollowed $event);
    public function projectTeamUpdated(TeamUpdated $event);
    public function projectTeamDeleted(TeamDeleted $event);
    public function projectFollowDeleted(FollowDeleted $event);
}
