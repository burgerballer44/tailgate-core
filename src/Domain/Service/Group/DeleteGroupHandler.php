<?php

namespace Tailgate\Domain\Service\Group;

use Tailgate\Application\Command\Group\DeleteGroupCommand;
use Tailgate\Application\Validator\ValidatorInterface;
use Tailgate\Domain\Model\Group\Group;
use Tailgate\Domain\Model\Group\GroupId;
use Tailgate\Domain\Model\Group\GroupRepositoryInterface;
use Tailgate\Domain\Service\AbstractService;

class DeleteGroupHandler extends AbstractService
{
    private $groupRepository;

    public function __construct(ValidatorInterface $validator, GroupRepositoryInterface $groupRepository)
    {
        parent::__construct($validator);
        $this->groupRepository = $groupRepository;
    }

    public function handle(DeleteGroupCommand $command)
    {
        $this->validate($command);

        $groupId = $command->getGroupId();

        $group = $this->groupRepository->get(GroupId::fromString($groupId));

        $group->delete();

        $this->groupRepository->add($group);
    }
}
