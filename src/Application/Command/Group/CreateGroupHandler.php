<?php

namespace Tailgate\Application\Command\Group;

use Tailgate\Domain\Model\Group\Group;
use Tailgate\Domain\Model\Group\GroupRepositoryInterface;

class CreateGroupHandler
{
    public $groupRepository;

    public function __construct(GroupRepositoryInterface $groupRepository)
    {
        $this->groupRepository = $groupRepository;
    }

    public function handle(CreateGroupCommand $createGroupCommand)
    {
        $name = $createGroupCommand->getName();

        $group = Group::create(
            $this->groupRepository->nextIdentity(),
            $name
        );
        $this->groupRepository->add($group);
    }
}