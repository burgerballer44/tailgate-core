<?php

namespace Tailgate\Domain\Service\Group;

use Tailgate\Application\Command\Group\UpdateScoreForGroupCommand;
use Tailgate\Application\Validator\ValidatorInterface;
use Tailgate\Domain\Model\Group\GroupId;
use Tailgate\Domain\Model\Group\GroupRepositoryInterface;
use Tailgate\Domain\Model\Group\ScoreId;
use Tailgate\Domain\Service\Validatable;
use Tailgate\Domain\Service\ValidatableService;

class UpdateScoreForGroupHandler implements ValidatableService
{
    use Validatable;
    
    private $validator;
    private $groupRepository;

    public function __construct(ValidatorInterface $validator, GroupRepositoryInterface $groupRepository)
    {
        $this->validator = $validator;
        $this->groupRepository = $groupRepository;
    }

    public function handle(UpdateScoreForGroupCommand $command)
    {
        $this->validate($command);

        $groupId = $command->getGroupId();
        $scoreId = $command->getScoreId();
        $homeTeamPrediction = $command->getHomeTeamPrediction();
        $awayTeamPrediction = $command->getAwayTeamPrediction();

        $group = $this->groupRepository->get(GroupId::fromString($groupId));

        $group->updateScore(
            ScoreId::fromString($scoreId),
            $homeTeamPrediction,
            $awayTeamPrediction
        );

        $this->groupRepository->add($group);
    }
}
