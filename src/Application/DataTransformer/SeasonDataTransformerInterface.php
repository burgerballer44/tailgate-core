<?php

namespace Tailgate\Application\DataTransformer;

use Tailgate\Domain\Model\Season\SeasonView;

interface SeasonDataTransformerInterface
{
    public function read(SeasonView $seasonView);
}