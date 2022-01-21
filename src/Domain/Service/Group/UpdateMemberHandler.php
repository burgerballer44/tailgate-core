<?php

namespace Tailgate\Domain\Service\Group;

use Tailgate\Application\Command\Group\UpdateMemberCommand;
use Tailgate\Application\Validator\ValidatorInterface;
use Tailgate\Domain\Model\Common\Date;
use Tailgate\Domain\Model\Group\GroupId;
use Tailgate\Domain\Model\Group\GroupRepositoryInterface;
use Tailgate\Domain\Model\Group\MemberId;
use Tailgate\Domain\Model\Season\GameId;
use Tailgate\Domain\Service\Clock\Clock;
use Tailgate\Domain\Service\Validatable;
use Tailgate\Domain\Service\ValidatableService;

class UpdateMemberHandler implements ValidatableService
{
    use Validatable;
    
    private $validator;
    private $clock;
    private $groupRepository;

    public function __construct(ValidatorInterface $validator, Clock $clock, GroupRepositoryInterface $groupRepository)
    {
        $this->validator = $validator;
        $this->clock = $clock;
        $this->groupRepository = $groupRepository;
    }

    public function handle(UpdateMemberCommand $command)
    {
        $this->validate($command);

        $group = $this->groupRepository->get(GroupId::fromString($command->getGroupId()));

        $group->updateMember(
            MemberId::fromString($command->getMemberId()),
            $command->getGroupRole(),
            $command->getAllowMultiplePlayers(),
            Date::fromDateTimeImmutable($this->clock->currentTime())
        );

        $this->groupRepository->add($group);
    }
}
