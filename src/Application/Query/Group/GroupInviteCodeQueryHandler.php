<?php

namespace Tailgate\Application\Query\Group;

use Tailgate\Domain\Model\Group\GroupViewRepositoryInterface;
use Tailgate\Application\DataTransformer\GroupDataTransformerInterface;

class GroupInviteCodeQueryHandler
{
    private $groupViewRepository;
    private $groupViewTransformer;

    public function __construct(
        GroupViewRepositoryInterface $groupViewRepository,
        GroupDataTransformerInterface $groupViewTransformer
    ) {
        $this->groupViewRepository = $groupViewRepository;
        $this->groupViewTransformer = $groupViewTransformer;
    }

    public function handle(GroupInviteCodeQuery $query)
    {
        $inviteCode = $query->getInviteCode();

        $groupView = $this->groupViewRepository->byInviteCode($inviteCode);
        return $this->groupViewTransformer->read($groupView);
    }
}
