<?php

namespace Tailgate\Application\DataTransformer;

use Tailgate\Application\DataTransformer\MemberDataTransformerInterface;
use Tailgate\Domain\Model\Group\MemberView;

class MemberViewArrayDataTransformer implements MemberDataTransformerInterface
{
    public function read(MemberView $memberView)
    {
        return [
            'memberId' => $memberView->getMemberId(),
            'groupId' => $memberView->getGroupId(),
            'userId' => $memberView->getUserId(),
            'role' => $memberView->getRole()
        ];
    }
}