<?php

namespace Tailgate\Domain\Service\Group;

use Tailgate\Application\Command\Group\CreateGroupCommand;
use Tailgate\Domain\Model\Common\Date;
use Tailgate\Domain\Model\Group\Group;
use Tailgate\Domain\Model\Group\GroupInviteCode;
use Tailgate\Domain\Model\Group\GroupRepositoryInterface;
use Tailgate\Domain\Model\User\UserId;
use Tailgate\Domain\Service\Clock\Clock;

class CreateGroupHandler
{
    private $clock;
    private $groupRepository;

    public function __construct(Clock $clock, GroupRepositoryInterface $groupRepository)
    {
        $this->clock = $clock;
        $this->groupRepository = $groupRepository;
    }

    public function handle(CreateGroupCommand $command)
    {
        $group = Group::create(
            $this->groupRepository->nextIdentity(),
            $command->getName(),
            GroupInviteCode::create(),
            UserId::fromString($command->getOwnerId()),
            Date::fromDateTimeImmutable($this->clock->currentTime())
        );

        $this->groupRepository->add($group);
    }
}
