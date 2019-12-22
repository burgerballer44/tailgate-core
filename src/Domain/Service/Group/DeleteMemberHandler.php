<?php

namespace Tailgate\Domain\Service\Group;

use Tailgate\Application\Command\Group\DeleteMemberCommand;
use Tailgate\Application\Validator\ValidatorInterface;
use Tailgate\Domain\Model\Group\Group;
use Tailgate\Domain\Model\Group\GroupId;
use Tailgate\Domain\Model\Group\GroupRepositoryInterface;
use Tailgate\Domain\Model\Group\MemberId;
use Tailgate\Domain\Service\AbstractService;

class DeleteMemberHandler extends AbstractService
{
    private $groupRepository;

    public function __construct(ValidatorInterface $validator, GroupRepositoryInterface $groupRepository)
    {
        parent::__construct($validator);
        $this->groupRepository = $groupRepository;
    }

    public function handle(DeleteMemberCommand $command)
    {
        $this->validate($command);

        $groupId = $command->getGroupId();
        $memberId = $command->getMemberId();

        $group = $this->groupRepository->get(GroupId::fromString($groupId));

        $group->deleteMember(MemberId::fromString($memberId));

        $this->groupRepository->add($group);
    }
}
