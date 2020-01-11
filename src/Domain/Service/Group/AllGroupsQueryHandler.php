<?php

namespace Tailgate\Domain\Service\Group;

use Tailgate\Application\Query\Group\AllGroupsQuery;
use Tailgate\Domain\Model\Group\GroupViewRepositoryInterface;
use Tailgate\Domain\Service\AbstractService;
use Tailgate\Domain\Service\DataTransformer\GroupDataTransformerInterface;

class AllGroupsQueryHandler extends AbstractService
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

    public function handle(AllGroupsQuery $query)
    {
        $groupViews = $this->groupViewRepository->all();

        $groups = [];

        foreach ($groupViews as $groupView) {
            $groups[] = $this->groupViewTransformer->read($groupView);
        }

        return $groups;
    }
}