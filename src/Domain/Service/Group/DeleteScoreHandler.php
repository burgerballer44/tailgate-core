<?php

namespace Tailgate\Domain\Service\Group;

use Tailgate\Application\Command\Group\DeleteScoreCommand;
use Tailgate\Application\Validator\ValidatorInterface;
use Tailgate\Domain\Model\Common\Date;
use Tailgate\Domain\Model\Group\Group;
use Tailgate\Domain\Model\Group\GroupId;
use Tailgate\Domain\Model\Group\GroupRepositoryInterface;
use Tailgate\Domain\Model\Group\ScoreId;
use Tailgate\Domain\Service\Clock\Clock;

class DeleteScoreHandler
{
    private $groupRepository;
    private $clock;

    public function __construct(GroupRepositoryInterface $groupRepository, Clock $clock)
    {
        $this->groupRepository = $groupRepository;
        $this->clock = $clock;
    }

    public function handle(DeleteScoreCommand $command)
    {
        $group = $this->groupRepository->get(GroupId::fromString($command->getGroupId()));

        $group->deleteScore(ScoreId::fromString($command->getScoreId()), Date::fromDateTimeImmutable($this->clock->currentTime()));

        $this->groupRepository->add($group);
    }
}
