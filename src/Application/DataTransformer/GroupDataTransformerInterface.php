<?php

namespace Tailgate\Application\DataTransformer;

use Tailgate\Domain\Model\Group\GroupView;

interface GroupDataTransformerInterface
{
    public function read(GroupView $groupView);
}
