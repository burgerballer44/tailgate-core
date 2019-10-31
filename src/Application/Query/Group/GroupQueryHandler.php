<?php

namespace Tailgate\Application\Query\Group;

use Tailgate\Domain\Model\User\UserId;
use Tailgate\Domain\Model\Group\GroupId;
use Tailgate\Domain\Model\Group\GroupViewRepositoryInterface;
use Tailgate\Domain\Model\Group\MemberViewRepositoryInterface;
use Tailgate\Domain\Model\Group\PlayerViewRepositoryInterface;
use Tailgate\Domain\Model\Group\ScoreViewRepositoryInterface;
use Tailgate\Application\DataTransformer\GroupDataTransformerInterface;

class GroupQueryHandler
{
    private $groupViewRepository;
    private $memberViewRepository;
    private $playerViewRepository;
    private $scoreViewRepository;
    private $groupViewTransformer;

    public function __construct(
        GroupViewRepositoryInterface $groupViewRepository,
        MemberViewRepositoryInterface $memberViewRepository,
        PlayerViewRepositoryInterface $playerViewRepository,
        ScoreViewRepositoryInterface $scoreViewRepository,
        GroupDataTransformerInterface $groupViewTransformer
    ) {
        $this->groupViewRepository = $groupViewRepository;
        $this->memberViewRepository = $memberViewRepository;
        $this->playerViewRepository = $playerViewRepository;
        $this->scoreViewRepository = $scoreViewRepository;
        $this->groupViewTransformer = $groupViewTransformer;
    }

    public function handle(GroupQuery $query)
    {
        $userId = UserId::fromString($query->getUserId());
        $groupId = GroupId::fromString($query->getGroupId());

        $groupView = $this->groupViewRepository->getByUser($userId, $groupId);
        $memberViews = $this->memberViewRepository->getAllByGroup($groupId);
        $playerViews = $this->playerViewRepository->getAllByGroup($groupId);
        $scoreViews = $this->scoreViewRepository->getAllByGroup($groupId);

        $groupView->addMemberViews($memberViews);
        $groupView->addPlayerViews($playerViews);
        $groupView->addScoreViews($scoreViews);

        return $this->groupViewTransformer->read($groupView);
    }
}
