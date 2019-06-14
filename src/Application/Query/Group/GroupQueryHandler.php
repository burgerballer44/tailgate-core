<?php

namespace Tailgate\Application\Query\Group;

use Tailgate\Domain\Model\Group\GroupId;
use Tailgate\Domain\Model\Group\GroupViewRepositoryInterface;

class GroupQueryHandler
{
    private $groupViewRepository;

    public function __construct(GroupViewRepositoryInterface $groupViewRepository)
    {
        $this->groupViewRepository = $groupViewRepository;
    }

    public function handle(GroupQuery $groupQuery)
    {
        return $this->groupViewRepository->get(GroupId::fromString($groupQuery->getGroupId()));
    }
}