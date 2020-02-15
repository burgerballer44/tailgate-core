<?php

namespace Tailgate\Domain\Service\Group;

use Tailgate\Application\Command\Group\DeleteScoreCommand;
use Tailgate\Application\Validator\ValidatorInterface;
use Tailgate\Domain\Model\Group\Group;
use Tailgate\Domain\Model\Group\GroupId;
use Tailgate\Domain\Model\Group\GroupRepositoryInterface;
use Tailgate\Domain\Model\Group\ScoreId;

class DeleteScoreHandler
{
    private $groupRepository;

    public function __construct(GroupRepositoryInterface $groupRepository)
    {
        $this->groupRepository = $groupRepository;
    }

    public function handle(DeleteScoreCommand $command)
    {
        $groupId = $command->getGroupId();
        $scoreId = $command->getScoreId();

        $group = $this->groupRepository->get(GroupId::fromString($groupId));

        $group->deleteScore(ScoreId::fromString($scoreId));

        $this->groupRepository->add($group);
    }
}
