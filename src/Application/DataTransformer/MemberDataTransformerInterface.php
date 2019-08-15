<?php

namespace Tailgate\Application\DataTransformer;

use Tailgate\Domain\Model\Group\MemberView;

interface MemberDataTransformerInterface
{
    public function read(MemberView $memberView);
}
