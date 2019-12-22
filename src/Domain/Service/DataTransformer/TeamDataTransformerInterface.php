<?php

namespace Tailgate\Domain\Service\DataTransformer;

use Tailgate\Domain\Model\Team\TeamView;

interface TeamDataTransformerInterface
{
    public function read(TeamView $teamView);
}
