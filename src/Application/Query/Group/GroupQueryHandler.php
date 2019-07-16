<?php

namespace Tailgate\Application\Query\Group;

use Tailgate\Domain\Model\Group\GroupId;
use Tailgate\Domain\Model\Group\GroupViewRepositoryInterface;
use Tailgate\Application\DataTransformer\GroupDataTransformerInterface;

class GroupQueryHandler
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

    public function handle(GroupQuery $groupQuery)
    {
        $groupView = $this->groupViewRepository->get(GroupId::fromString($groupQuery->getGroupId()));
        return $this->groupViewTransformer->read($groupView);
    }
}