<?php

namespace Tailgate\Domain\Service\DataTransformer;

use Tailgate\Domain\Model\Group\MemberView;

interface MemberDataTransformerInterface
{
    public function read(MemberView $memberView);
}
