<?php

namespace Tailgate\Domain\Service\Group;

use Tailgate\Application\Command\Group\FollowTeamCommand;
use Tailgate\Application\Validator\ValidatorInterface;
use Tailgate\Domain\Model\Group\GroupId;
use Tailgate\Domain\Model\Group\GroupRepositoryInterface;
use Tailgate\Domain\Model\Season\SeasonId;
use Tailgate\Domain\Model\Team\TeamId;
use Tailgate\Domain\Service\Validatable;
use Tailgate\Domain\Service\ValidatableService;

class FollowTeamHandler implements ValidatableService
{
    use Validatable;
    
    private $validator;
    private $groupRepository;

    public function __construct(ValidatorInterface $validator, GroupRepositoryInterface $groupRepository)
    {
        $this->validator = $validator;
        $this->groupRepository = $groupRepository;
    }

    public function handle(FollowTeamCommand $command)
    {
        $this->validate($command);

        $groupId = $command->getGroupId();
        $seasonId = $command->getSeasonId();
        $teamId = $command->getTeamId();

        $group = $this->groupRepository->get(GroupId::fromString($groupId));

        $group->followTeam(TeamId::fromString($teamId), SeasonId::fromString($seasonId));
        
        $this->groupRepository->add($group);
    }
}
