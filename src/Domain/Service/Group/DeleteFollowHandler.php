<?php

namespace Tailgate\Domain\Service\Group;

use Tailgate\Application\Command\Group\DeleteFollowCommand;
use Tailgate\Domain\Model\Common\Date;
use Tailgate\Domain\Model\Group\FollowId;
use Tailgate\Domain\Model\Group\GroupId;
use Tailgate\Domain\Model\Group\GroupRepositoryInterface;
use Tailgate\Domain\Service\Clock\Clock;

class DeleteFollowHandler
{
    private $groupRepository;
    private $clock;

    public function __construct(GroupRepositoryInterface $groupRepository, Clock $clock)
    {
        $this->groupRepository = $groupRepository;
        $this->clock = $clock;
    }

    public function handle(DeleteFollowCommand $command)
    {
        $group = $this->groupRepository->get(GroupId::fromString($command->getGroupId()));

        $group->deleteFollow(FollowId::fromString($command->getFollowId()), Date::fromDateTimeImmutable($this->clock->currentTime()));

        $this->groupRepository->add($group);
    }
}
