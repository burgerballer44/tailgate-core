<?php

namespace Tailgate\Domain\Service\Group;

use Tailgate\Application\Command\Group\CreateGroupCommand;
use Tailgate\Application\Validator\ValidatorInterface;
use Tailgate\Domain\Model\Common\Date;
use Tailgate\Domain\Model\Group\Group;
use Tailgate\Domain\Model\Group\GroupInviteCode;
use Tailgate\Domain\Model\Group\GroupRepositoryInterface;
use Tailgate\Domain\Model\User\UserId;
use Tailgate\Domain\Service\Clock\Clock;
use Tailgate\Domain\Service\Validatable;
use Tailgate\Domain\Service\ValidatableService;

class CreateGroupHandler implements ValidatableService
{
    use Validatable;
    
    private $validator;
    private $clock;
    private $groupRepository;

    public function __construct(
        ValidatorInterface $validator,
        Clock $clock,
        GroupRepositoryInterface $groupRepository
    ) {
        $this->validator = $validator;
        $this->clock = $clock;
        $this->groupRepository = $groupRepository;
    }

    public function handle(CreateGroupCommand $command)
    {
        $this->validate($command);

        $group = Group::create(
            $this->groupRepository->nextIdentity(),
            $command->getName(),
            GroupInviteCode::create(),
            UserId::fromString($command->getOwnerId()),
            Date::fromDateTimeImmutable($this->clock->currentTime())
        );
        
        $this->groupRepository->add($group);
    }
}
