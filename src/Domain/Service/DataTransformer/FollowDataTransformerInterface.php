<?php

namespace Tailgate\Domain\Service\DataTransformer;

use Tailgate\Domain\Model\Group\FollowView;

interface FollowDataTransformerInterface
{
    public function read(FollowView $FollowView);
}
