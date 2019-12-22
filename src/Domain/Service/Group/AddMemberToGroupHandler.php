<?php

namespace Tailgate\Domain\Service\Group;

use Tailgate\Application\Command\Group\AddMemberToGroupCommand;
use Tailgate\Application\Validator\ValidatorInterface;
use Tailgate\Domain\Model\Group\GroupId;
use Tailgate\Domain\Model\Group\GroupRepositoryInterface;
use Tailgate\Domain\Model\User\UserId;
use Tailgate\Domain\Service\AbstractService;

class AddMemberToGroupHandler extends AbstractService
{
    public $groupRepository;

    public function __construct(ValidatorInterface $validator, GroupRepositoryInterface $groupRepository)
    {
        parent::__construct($validator);
        $this->groupRepository = $groupRepository;
    }

    public function handle(AddMemberToGroupCommand $command)
    {
        $this->validate($command);

        $groupId = $command->getGroupId();
        $userId = $command->getUserId();

        $group = $this->groupRepository->get(GroupId::fromString($groupId));

        $group->addMember(UserId::fromString($userId));

        $this->groupRepository->add($group);
    }
}
