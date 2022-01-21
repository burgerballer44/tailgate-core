<?php

namespace Tailgate\Domain\Service\Group;

use Tailgate\Application\Command\Group\DeleteMemberCommand;
use Tailgate\Application\Validator\ValidatorInterface;
use Tailgate\Domain\Model\Common\Date;
use Tailgate\Domain\Model\Group\Group;
use Tailgate\Domain\Model\Group\GroupId;
use Tailgate\Domain\Model\Group\GroupRepositoryInterface;
use Tailgate\Domain\Model\Group\MemberId;
use Tailgate\Domain\Service\Clock\Clock;

class DeleteMemberHandler
{
    private $groupRepository;
    private $clock;

    public function __construct(GroupRepositoryInterface $groupRepository, Clock $clock)
    {
        $this->groupRepository = $groupRepository;
        $this->clock = $clock;
    }

    public function handle(DeleteMemberCommand $command)
    {
        $group = $this->groupRepository->get(GroupId::fromString($command->getGroupId()));

        $group->deleteMember(MemberId::fromString($command->getMemberId()), Date::fromDateTimeImmutable($this->clock->currentTime()));

        $this->groupRepository->add($group);
    }
}
