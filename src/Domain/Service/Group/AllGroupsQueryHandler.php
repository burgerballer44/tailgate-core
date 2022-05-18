<?php

namespace Tailgate\Domain\Service\Group;

use Tailgate\Domain\Model\Group\GroupViewRepositoryInterface;
use Tailgate\Domain\Service\DataTransformer\GroupDataTransformerInterface;

class AllGroupsQueryHandler
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

    public function handle()
    {
        $groupViews = $this->groupViewRepository->all();

        $groups = [];

        foreach ($groupViews as $groupView) {
            $groups[] = $this->groupViewTransformer->read($groupView);
        }

        return $groups;
    }
}
