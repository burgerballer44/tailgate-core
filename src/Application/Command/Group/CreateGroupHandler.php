<?php

namespace Tailgate\Application\Command\Group;

use Tailgate\Domain\Model\Group\Group;
use Tailgate\Domain\Model\Group\GroupRepositoryInterface;
use Tailgate\Domain\Model\User\UserId;

class CreateGroupHandler
{
    public $groupRepository;

    public function __construct(GroupRepositoryInterface $groupRepository)
    {
        $this->groupRepository = $groupRepository;
    }

    public function handle(CreateGroupCommand $command)
    {
        $name = $command->getName();
        $ownerId = $command->getOwnerId();

        $group = Group::create(
            $this->groupRepository->nextIdentity(),
            $name,
            UserId::fromString($ownerId)
        );
        
        $this->groupRepository->add($group);
    }
}
