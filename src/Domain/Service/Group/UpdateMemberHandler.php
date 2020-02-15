<?php

namespace Tailgate\Domain\Service\Group;

use Tailgate\Application\Command\Group\UpdateMemberCommand;
use Tailgate\Application\Validator\ValidatorInterface;
use Tailgate\Domain\Model\Group\GroupId;
use Tailgate\Domain\Model\Group\GroupRepositoryInterface;
use Tailgate\Domain\Model\Group\MemberId;
use Tailgate\Domain\Model\Season\GameId;
use Tailgate\Domain\Service\Validatable;
use Tailgate\Domain\Service\ValidatableService;

class UpdateMemberHandler implements ValidatableService
{
    use Validatable;
    
    private $validator;
    private $groupRepository;

    public function __construct(ValidatorInterface $validator, GroupRepositoryInterface $groupRepository)
    {
        $this->validator = $validator;
        $this->groupRepository = $groupRepository;
    }

    public function handle(UpdateMemberCommand $command)
    {
        $this->validate($command);

        $groupId = $command->getGroupId();
        $memberId = $command->getMemberId();
        $groupRole = $command->getGroupRole();
        $allowMultiplePlayers = $command->getAllowMultiplePlayers();

        $group = $this->groupRepository->get(GroupId::fromString($groupId));

        $group->updateMember(
            MemberId::fromString($memberId),
            $groupRole,
            $allowMultiplePlayers
        );

        $this->groupRepository->add($group);
    }
}
