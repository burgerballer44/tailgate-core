<?php

namespace Tailgate\Infrastructure\Persistence\Projection;

use Tailgate\Domain\Model\Team\TeamAdded;
use Tailgate\Domain\Model\Team\TeamDeleted;
use Tailgate\Domain\Model\Team\TeamUpdated;

interface TeamProjectionInterface extends ProjectionInterface
{
    public function projectTeamAdded(TeamAdded $event);

    public function projectTeamUpdated(TeamUpdated $event);

    public function projectTeamDeleted(TeamDeleted $event);
}
