<?php

namespace Tailgate\Application\Query\Group;

use Tailgate\Domain\Model\Group\GroupId;
use Tailgate\Domain\Model\Group\GroupViewRepositoryInterface;
use Tailgate\Domain\Model\Group\MemberViewRepositoryInterface;
use Tailgate\Domain\Model\Group\ScoreViewRepositoryInterface;
use Tailgate\Application\DataTransformer\GroupDataTransformerInterface;
use Tailgate\Application\DataTransformer\MemberDataTransformerInterface;
use Tailgate\Application\DataTransformer\ScoreDataTransformerInterface;

class GroupQueryHandler
{
    private $groupViewRepository;
    private $memberViewRepository;
    private $scoreViewRepository;
    private $groupViewTransformer;
    private $memberViewTransformer;
    private $scoreViewTransformer;

    public function __construct(
        GroupViewRepositoryInterface $groupViewRepository,
        MemberViewRepositoryInterface $memberViewRepository,
        ScoreViewRepositoryInterface $scoreViewRepository,
        GroupDataTransformerInterface $groupViewTransformer,
        MemberDataTransformerInterface $memberViewTransformer,
        ScoreDataTransformerInterface $scoreViewTransformer
    ) {
        $this->groupViewRepository = $groupViewRepository;
        $this->memberViewRepository = $memberViewRepository;
        $this->scoreViewRepository = $scoreViewRepository;
        $this->groupViewTransformer = $groupViewTransformer;
        $this->memberViewTransformer = $memberViewTransformer;
        $this->scoreViewTransformer = $scoreViewTransformer;
    }

    public function handle(GroupQuery $groupQuery)
    {
        $groupId = GroupId::fromString($groupQuery->getGroupId());

        $groupView = $this->groupViewRepository->get($groupId);

        $memberViews = $this->memberViewRepository->getAllByGroup($groupId);
        $members = [];
        foreach ($memberViews as $memberView) {
            $members[] = $this->memberViewTransformer->read($memberView);
        }

        $scoreViews = $this->scoreViewRepository->getAllByGroup($groupId);
        $scores = [];
        foreach ($scoreViews as $scoreView) {
            $scores[] = $this->scoreViewTransformer->read($scoreView);
        }

        return [
            $this->groupViewTransformer->read($groupView),
            'members' => $members,
            'scores' => $scores,
        ];
    }
}