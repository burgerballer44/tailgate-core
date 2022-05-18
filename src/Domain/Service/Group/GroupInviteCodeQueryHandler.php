<?php

namespace Tailgate\Domain\Service\Group;

use Tailgate\Application\Query\Group\GroupInviteCodeQuery;
use Tailgate\Domain\Model\Group\GroupViewRepositoryInterface;
use Tailgate\Domain\Service\DataTransformer\GroupDataTransformerInterface;

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
        $groupView = $this->groupViewRepository->byInviteCode($query->getInviteCode());

        return $this->groupViewTransformer->read($groupView);
    }
}
