<?php

namespace Tailgate\Domain\Service\Group;

use Tailgate\Application\Command\Group\ChangePlayerOwnerCommand;
use Tailgate\Domain\Model\Common\Date;
use Tailgate\Domain\Model\Group\GroupId;
use Tailgate\Domain\Model\Group\GroupRepositoryInterface;
use Tailgate\Domain\Model\Group\MemberId;
use Tailgate\Domain\Model\Group\PlayerId;
use Tailgate\Domain\Service\Clock\Clock;

class ChangePlayerOwnerHandler
{
    private $clock;
    private $groupRepository;

    public function __construct(Clock $clock, GroupRepositoryInterface $groupRepository)
    {
        $this->clock = $clock;
        $this->groupRepository = $groupRepository;
    }

    public function handle(ChangePlayerOwnerCommand $command)
    {
        $group = $this->groupRepository->get(GroupId::fromString($command->getGroupId()));

        $group->changePlayerOwner(PlayerId::fromString($command->getPlayerId()), MemberId::fromString($command->getMemberId()), Date::fromDateTimeImmutable($this->clock->currentTime()));

        $this->groupRepository->add($group);
    }
}
