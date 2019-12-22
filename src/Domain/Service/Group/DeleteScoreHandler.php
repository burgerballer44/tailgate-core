<?php

namespace Tailgate\Domain\Service\Group;

use Tailgate\Application\Command\Group\DeleteScoreCommand;
use Tailgate\Application\Validator\ValidatorInterface;
use Tailgate\Domain\Model\Group\Group;
use Tailgate\Domain\Model\Group\GroupId;
use Tailgate\Domain\Model\Group\GroupRepositoryInterface;
use Tailgate\Domain\Model\Group\ScoreId;
use Tailgate\Domain\Service\AbstractService;

class DeleteScoreHandler extends AbstractService
{
    private $groupRepository;

    public function __construct(ValidatorInterface $validator, GroupRepositoryInterface $groupRepository)
    {
        parent::__construct($validator);
        $this->groupRepository = $groupRepository;
    }

    public function handle(DeleteScoreCommand $command)
    {
        $this->validate($command);

        $groupId = $command->getGroupId();
        $scoreId = $command->getScoreId();

        $group = $this->groupRepository->get(GroupId::fromString($groupId));

        $group->deleteScore(ScoreId::fromString($scoreId));

        $this->groupRepository->add($group);
    }
}
