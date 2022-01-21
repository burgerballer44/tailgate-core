<?php

namespace Tailgate\Domain\Service\Group;

use Tailgate\Application\Command\Group\DeletePlayerCommand;
use Tailgate\Application\Validator\ValidatorInterface;
use Tailgate\Domain\Model\Common\Date;
use Tailgate\Domain\Model\Group\Group;
use Tailgate\Domain\Model\Group\GroupId;
use Tailgate\Domain\Model\Group\GroupRepositoryInterface;
use Tailgate\Domain\Model\Group\PlayerId;
use Tailgate\Domain\Service\Clock\Clock;

class DeletePlayerHandler
{
    private $groupRepository;
    private $clock;

    public function __construct(GroupRepositoryInterface $groupRepository, Clock $clock)
    {
        $this->groupRepository = $groupRepository;
        $this->clock = $clock;
    }

    public function handle(DeletePlayerCommand $command)
    {
        $group = $this->groupRepository->get(GroupId::fromString($command->getGroupId()));

        $group->deletePlayer(PlayerId::fromString($command->getPlayerId()), Date::fromDateTimeImmutable($this->clock->currentTime()));

        $this->groupRepository->add($group);
    }
}
