<?php

namespace Tailgate\Domain\Service\DataTransformer;

use Tailgate\Domain\Model\Group\ScoreView;

interface ScoreDataTransformerInterface
{
    public function read(ScoreView $scoreView);
}
