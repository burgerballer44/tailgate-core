<?php

namespace Tailgate\Application\Query\Group;

use Tailgate\Application\DataTransformer\GroupDataTransformerInterface;
use Tailgate\Domain\Model\Group\GroupViewRepositoryInterface;
use Tailgate\Domain\Model\User\UserId;

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
        $userId = UserId::fromString($query->getUserId());

        $groupViews = $this->groupViewRepository->allByUser($userId);

        $groups = [];

        foreach ($groupViews as $groupView) {
            $groups[] = $this->groupViewTransformer->read($groupView);
        }

        return $groups;
    }
}