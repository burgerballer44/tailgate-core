<?php

namespace Tailgate\Infrastructure\Service\DataTransformer;

use Tailgate\Domain\Model\Group\MemberView;
use Tailgate\Domain\Service\DataTransformer\MemberDataTransformerInterface;

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
            'email' => $memberView->getEmail(),
        ];
    }
}
