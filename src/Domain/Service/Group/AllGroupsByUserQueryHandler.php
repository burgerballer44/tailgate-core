<?php

namespace Tailgate\Domain\Service\Group;

use Tailgate\Application\Query\Group\AllGroupsByUserQuery;
use Tailgate\Domain\Model\Group\GroupViewRepositoryInterface;
use Tailgate\Domain\Model\User\UserId;
use Tailgate\Domain\Service\DataTransformer\GroupDataTransformerInterface;

class AllGroupsByUserQueryHandler
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

    public function handle(AllGroupsByUserQuery $query)
    {
        $groupViews = $this->groupViewRepository->allByUser(UserId::fromString($query->getUserId()));

        $groups = [];

        foreach ($groupViews as $groupView) {
            $groups[] = $this->groupViewTransformer->read($groupView);
        }

        return $groups;
    }
}
