<?php

namespace Tailgate\Application\Command\Group;

use Tailgate\Domain\Model\Group\Group;
use Tailgate\Domain\Model\Group\GroupRepositoryInterface;
use Tailgate\Domain\Model\User\UserId;
use Tailgate\Common\Security\RandomStringInterface;

class CreateGroupHandler
{
    public $groupRepository;
    private $randomStringer;

    public function __construct(
        GroupRepositoryInterface $groupRepository,
        RandomStringInterface $randomStringer
    ) {
        $this->groupRepository = $groupRepository;
        $this->randomStringer = $randomStringer;
    }

    public function handle(CreateGroupCommand $command)
    {
        $name = $command->getName();
        $ownerId = $command->getOwnerId();

        $group = Group::create(
            $this->groupRepository->nextIdentity(),
            $name,
            $this->randomStringer->generate(),
            UserId::fromString($ownerId)
        );
        
        $this->groupRepository->add($group);
    }
}
