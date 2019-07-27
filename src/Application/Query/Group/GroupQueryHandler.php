<?php

namespace Tailgate\Application\Query\Group;

use Tailgate\Domain\Model\Group\GroupId;
use Tailgate\Domain\Model\Group\GroupViewRepositoryInterface;
use Tailgate\Domain\Model\Group\MemberViewRepositoryInterface;
use Tailgate\Domain\Model\Group\ScoreViewRepositoryInterface;
use Tailgate\Application\DataTransformer\GroupDataTransformerInterface;

class GroupQueryHandler
{
    private $groupViewRepository;
    private $memberViewRepository;
    private $scoreViewRepository;
    private $groupViewTransformer;

    public function __construct(
        GroupViewRepositoryInterface $groupViewRepository,
        MemberViewRepositoryInterface $memberViewRepository,
        ScoreViewRepositoryInterface $scoreViewRepository,
        GroupDataTransformerInterface $groupViewTransformer
    ) {
        $this->groupViewRepository = $groupViewRepository;
        $this->memberViewRepository = $memberViewRepository;
        $this->scoreViewRepository = $scoreViewRepository;
        $this->groupViewTransformer = $groupViewTransformer;
    }

    public function handle(GroupQuery $groupQuery)
    {
        $groupId = GroupId::fromString($groupQuery->getGroupId());

        $groupView = $this->groupViewRepository->get($groupId);
        $memberViews = $this->memberViewRepository->getAllByGroup($groupId);
        $scoreViews = $this->scoreViewRepository->getAllByGroup($groupId);

        $groupView->addMemberViews($memberViews);
        $groupView->addScoreViews($scoreViews);

        return $this->groupViewTransformer->read($groupView);
    }
}