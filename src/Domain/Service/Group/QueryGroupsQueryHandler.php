<?php

namespace Tailgate\Domain\Service\Group;

use Tailgate\Application\Query\Group\QueryGroupsQuery;
use Tailgate\Domain\Model\Group\GroupViewRepositoryInterface;
use Tailgate\Domain\Model\User\UserId;
use Tailgate\Domain\Service\DataTransformer\GroupDataTransformerInterface;

class QueryGroupsQueryHandler
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

    public function handle(QueryGroupsQuery $query)
    {
        $groupViews = $this->groupViewRepository->query(UserId::fromString($query->getUserId()), $query->getName());

        $groups = [];

        foreach ($groupViews as $groupView) {
            $groups[] = $this->groupViewTransformer->read($groupView);
        }

        return $groups;
    }
}
