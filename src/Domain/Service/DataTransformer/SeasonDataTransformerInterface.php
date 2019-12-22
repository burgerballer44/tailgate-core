<?php

namespace Tailgate\Domain\Service\DataTransformer;

use Tailgate\Domain\Model\Season\SeasonView;

interface SeasonDataTransformerInterface
{
    public function read(SeasonView $seasonView);
}
