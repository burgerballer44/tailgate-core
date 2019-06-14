<?php

namespace Tailgate\Application\Query\Group;

use Tailgate\Domain\Model\Group\GroupViewRepositoryInterface;

class AllGroupsQueryHandler
{
    private $groupViewRepository;

    public function __construct(GroupViewRepositoryInterface $groupViewRepository)
    {
        $this->groupViewRepository = $groupViewRepository;
    }

    public function handle(AllGroupsQuery $allGroupsQuery)
    {
        return $this->groupViewRepository->all();
    }
}