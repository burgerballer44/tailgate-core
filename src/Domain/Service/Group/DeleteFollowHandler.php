<?php

namespace Tailgate\Domain\Service\Group;

use Tailgate\Application\Command\Group\DeleteFollowCommand;
use Tailgate\Application\Validator\ValidatorInterface;
use Tailgate\Domain\Model\Group\FollowId;
use Tailgate\Domain\Model\Group\GroupId;
use Tailgate\Domain\Model\Group\GroupRepositoryInterface;
use Tailgate\Domain\Service\AbstractService;

class DeleteFollowHandler extends AbstractService
{
    private $groupRepository;

    public function __construct(ValidatorInterface $validator, GroupRepositoryInterface $groupRepository)
    {
        parent::__construct($validator);
        $this->groupRepository = $groupRepository;
    }

    public function handle(DeleteFollowCommand $command)
    {
        $this->validate($command);

        $groupId = $command->getGroupId();
        $followId = $command->getFollowId();

        $group = $this->groupRepository->get(GroupId::fromString($groupId));

        $group->deleteFollow(FollowId::fromString($followId));

        $this->groupRepository->add($group);
    }
}
