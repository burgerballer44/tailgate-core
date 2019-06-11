<?php

namespace Tailgate\Domain\Model\Team;

use Tailgate\Common\Projection\ProjectionInterface;

interface TeamProjectionInterface extends ProjectionInterface
{
    public function projectTeamFollowed(TeamFollowed $event);
    public function projectTeamAdded(TeamAdded $event);
}