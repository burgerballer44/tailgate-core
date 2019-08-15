<?php

namespace Tailgate\Application\DataTransformer;

use Tailgate\Domain\Model\Team\TeamView;

interface TeamDataTransformerInterface
{
    public function read(TeamView $teamView);
}
