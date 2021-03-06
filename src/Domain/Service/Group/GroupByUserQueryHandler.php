<?php

namespace Tailgate\Domain\Service\Group;

use Tailgate\Application\Query\Group\GroupByUserQuery;
use Tailgate\Domain\Model\Group\FollowViewRepositoryInterface;
use Tailgate\Domain\Model\Group\GroupId;
use Tailgate\Domain\Model\Group\GroupViewRepositoryInterface;
use Tailgate\Domain\Model\Group\MemberViewRepositoryInterface;
use Tailgate\Domain\Model\Group\PlayerViewRepositoryInterface;
use Tailgate\Domain\Model\Group\ScoreViewRepositoryInterface;
use Tailgate\Domain\Model\User\UserId;
use Tailgate\Domain\Service\DataTransformer\GroupDataTransformerInterface;

class GroupByUserQueryHandler
{
    private $groupViewRepository;
    private $memberViewRepository;
    private $playerViewRepository;
    private $scoreViewRepository;
    private $followViewRepository;
    private $groupViewTransformer;

    public function __construct(
        GroupViewRepositoryInterface $groupViewRepository,
        MemberViewRepositoryInterface $memberViewRepository,
        PlayerViewRepositoryInterface $playerViewRepository,
        ScoreViewRepositoryInterface $scoreViewRepository,
        FollowViewRepositoryInterface $followViewRepository,
        GroupDataTransformerInterface $groupViewTransformer
    ) {
        $this->groupViewRepository = $groupViewRepository;
        $this->memberViewRepository = $memberViewRepository;
        $this->playerViewRepository = $playerViewRepository;
        $this->scoreViewRepository = $scoreViewRepository;
        $this->followViewRepository = $followViewRepository;
        $this->groupViewTransformer = $groupViewTransformer;
    }

    public function handle(GroupByUserQuery $query)
    {
        $groupId = GroupId::fromString($query->getGroupId());

        $groupView = $this->groupViewRepository->getByUser(UserId::fromString($query->getUserId()), $groupId);
        $memberViews = $this->memberViewRepository->getAllByGroup($groupId);
        $playerViews = $this->playerViewRepository->getAllByGroup($groupId);
        $scoreViews = $this->scoreViewRepository->getAllByGroup($groupId);
        $followView = $this->followViewRepository->getByGroup($groupId);

        $groupView->addMemberViews($memberViews);
        $groupView->addPlayerViews($playerViews);
        $groupView->addScoreViews($scoreViews);
        $groupView->addFollowView($followView);

        return $this->groupViewTransformer->read($groupView);
    }
}
