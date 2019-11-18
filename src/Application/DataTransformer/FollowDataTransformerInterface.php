<?php

namespace Tailgate\Application\DataTransformer;

use Tailgate\Domain\Model\Group\FollowView;

interface FollowDataTransformerInterface
{
    public function read(FollowView $FollowView);
}
