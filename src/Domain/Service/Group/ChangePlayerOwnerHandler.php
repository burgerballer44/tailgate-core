<?php

namespace Tailgate\Domain\Service\Group;

use Tailgate\Application\Command\Group\ChangePlayerOwnerCommand;
use Tailgate\Application\Validator\ValidatorInterface;
use Tailgate\Domain\Model\Group\GroupId;
use Tailgate\Domain\Model\Group\GroupRepositoryInterface;
use Tailgate\Domain\Model\Group\MemberId;
use Tailgate\Domain\Model\Group\PlayerId;
use Tailgate\Domain\Service\Validatable;
use Tailgate\Domain\Service\ValidatableService;

class ChangePlayerOwnerHandler implements ValidatableService
{
    use Validatable;
    
    private $validator;
    private $groupRepository;

    public function __construct(ValidatorInterface $validator, GroupRepositoryInterface $groupRepository)
    {
        $this->validator = $validator;
        $this->groupRepository = $groupRepository;
    }

    public function handle(ChangePlayerOwnerCommand $command)
    {
        $this->validate($command);

        $groupId = $command->getGroupId();
        $memberId = $command->getMemberId();
        $playerId = $command->getPlayerId();

        $group = $this->groupRepository->get(GroupId::fromString($groupId));

        $group->changePlayerOwner(PlayerId::fromString($playerId), MemberId::fromString($memberId));

        $this->groupRepository->add($group);
    }
}
