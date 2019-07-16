<?php

namespace Tailgate\Application\DataTransformer;

use Tailgate\Application\DataTransformer\GroupDataTransformerInterface;
use Tailgate\Domain\Model\Group\GroupView;

class GroupViewArrayDataTransformer implements GroupDataTransformerInterface
{
    public function read(GroupView $groupView)
    {
        return [
            'groupId' => $groupView->getGroupId(),
            'name'    => $groupView->getName(),
            'ownerId' => $groupView->getOwnerId(),
        ];
    }
}