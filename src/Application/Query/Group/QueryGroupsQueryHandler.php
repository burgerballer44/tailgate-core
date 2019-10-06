<?php

namespace Tailgate\Application\Query\Group;

use Tailgate\Domain\Model\User\UserId;
use Tailgate\Domain\Model\Group\GroupViewRepositoryInterface;
use Tailgate\Application\DataTransformer\GroupDataTransformerInterface;

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
        $userId = UserId::fromString($query->getUserId());
        $name = $query->getName();

        $groupViews = $this->groupViewRepository->query($userId, $name);

        $groups = [];

        foreach ($groupViews as $groupView) {
            $groups[] = $this->groupViewTransformer->read($groupView);
        }

        return $groups;
    }
}
