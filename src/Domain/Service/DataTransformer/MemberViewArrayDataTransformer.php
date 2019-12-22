<?php

namespace Tailgate\Domain\Service\DataTransformer;

use Tailgate\Domain\Service\DataTransformer\MemberDataTransformerInterface;
use Tailgate\Domain\Model\Group\MemberView;

class MemberViewArrayDataTransformer implements MemberDataTransformerInterface
{
    public function read(MemberView $memberView)
    {
        return [
            'memberId' => $memberView->getMemberId(),
            'groupId' => $memberView->getGroupId(),
            'userId' => $memberView->getUserId(),
            'role' => $memberView->getRole(),
            'allowMultiplePlayers' => $memberView->getAllowMultiplePlayers(),
            'email' => $memberView->getEmail()
        ];
    }
}
