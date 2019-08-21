<?php

namespace Tailgate\Application\Command\Group;

use Tailgate\Domain\Model\Group\Group;
use Tailgate\Domain\Model\Group\GroupId;
use Tailgate\Domain\Model\Group\ScoreId;
use Tailgate\Domain\Model\Group\GroupRepositoryInterface;

class DeleteScoreHandler
{
    private $groupRepository;

    public function __construct(GroupRepositoryInterface $groupRepository)
    {
        $this->groupRepository = $groupRepository;
    }

    public function handle(DeleteGameCommand $command)
    {
        $groupId = $command->getGroupId();
        $scoreId = $command->getScoreId();

        $group = $this->groupRepository->get(GroupId::fromString($groupId));

        $group->deleteScore(ScoreId::fromString($scoreId));

        $this->groupRepository->add($group);
    }
}
