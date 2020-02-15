<?php

namespace Tailgate\Domain\Service\Group;

use Tailgate\Application\Command\Group\AddPlayerToGroupCommand;
use Tailgate\Application\Validator\ValidatorInterface;
use Tailgate\Domain\Model\Group\GroupId;
use Tailgate\Domain\Model\Group\GroupRepositoryInterface;
use Tailgate\Domain\Model\Group\MemberId;
use Tailgate\Domain\Service\Validatable;
use Tailgate\Domain\Service\ValidatableService;

class AddPlayerToGroupHandler implements ValidatableService
{
    use Validatable;
    
    private $validator;
    private $groupRepository;

    public function __construct(ValidatorInterface $validator, GroupRepositoryInterface $groupRepository)
    {
        $this->validator = $validator;
        $this->groupRepository = $groupRepository;
    }

    public function handle(AddPlayerToGroupCommand $command)
    {
        $this->validate($command);

        $groupId = $command->getGroupId();
        $memberId = $command->getMemberId();
        $username = $command->getUsername();

        $group = $this->groupRepository->get(GroupId::fromString($groupId));

        $group->addPlayer(MemberId::fromString($memberId), $username);

        $this->groupRepository->add($group);
    }
}
