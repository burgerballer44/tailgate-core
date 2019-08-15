<?php

namespace Tailgate\Application\DataTransformer;

use Tailgate\Domain\Model\Group\ScoreView;

interface ScoreDataTransformerInterface
{
    public function read(ScoreView $scoreView);
}
