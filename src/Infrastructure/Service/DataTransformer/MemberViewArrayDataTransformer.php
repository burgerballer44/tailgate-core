<?php

namespace Tailgate\Infrastructure\Service\DataTransformer;

use Tailgate\Domain\Model\Group\MemberView;
use Tailgate\Domain\Service\DataTransformer\MemberDataTransformerInterface;

class MemberViewArrayDataTransformer implements MemberDataTransformerInterface
{
    public function read(MemberView $memberView)
    {
        return [
            'member_id' => $memberView->getMemberId(),
            'group_id' => $memberView->getGroupId(),
            'user_id' => $memberView->getUserId(),
            'role' => $memberView->getRole(),
            'allow_multiple_players' => $memberView->getAllowMultiplePlayers(),
            'email' => $memberView->getEmail(),
        ];
    }
}
