<?php

namespace Tailgate\Application\DataTransformer;

use Tailgate\Domain\Model\Team\FollowView;

interface FollowDataTransformerInterface
{
    public function read(FollowView $FollowView);
}
