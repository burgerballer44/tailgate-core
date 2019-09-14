<?php

namespace Tailgate\Application\Command\Group;

use Tailgate\Domain\Model\Group\Group;
use Tailgate\Domain\Model\Group\GroupId;
use Tailgate\Domain\Model\Group\PlayerId;
use Tailgate\Domain\Model\Group\GroupRepositoryInterface;

class DeletePlayerHandler
{
    private $groupRepository;

    public function __construct(GroupRepositoryInterface $groupRepository)
    {
        $this->groupRepository = $groupRepository;
    }

    public function handle(DeletePlayerCommand $command)
    {
        $groupId = $command->getGroupId();
        $PlayerId = $command->getPlayerId();

        $group = $this->groupRepository->get(GroupId::fromString($groupId));

        $group->deletePlayer(PlayerId::fromString($PlayerId));

        $this->groupRepository->add($group);
    }
}
