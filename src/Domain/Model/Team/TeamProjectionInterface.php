<?php

namespace Tailgate\Domain\Model\Team;

use Tailgate\Common\Projection\ProjectionInterface;

interface TeamProjectionInterface extends ProjectionInterface
{
    public function projectTeamAdded(TeamAdded $event);
    public function projectTeamUpdated(TeamUpdated $event);
    public function projectTeamDeleted(TeamDeleted $event);
}
