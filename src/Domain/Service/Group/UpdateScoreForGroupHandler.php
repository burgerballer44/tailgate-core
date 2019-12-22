<?php

namespace Tailgate\Domain\Service\Group;

use Tailgate\Application\Command\Group\UpdateScoreForGroupCommand;
use Tailgate\Application\Validator\ValidatorInterface;
use Tailgate\Domain\Model\Group\GroupId;
use Tailgate\Domain\Model\Group\GroupRepositoryInterface;
use Tailgate\Domain\Model\Group\ScoreId;
use Tailgate\Domain\Service\AbstractService;

class UpdateScoreForGroupHandler extends AbstractService
{
    public $groupRepository;

    public function __construct(ValidatorInterface $validator, GroupRepositoryInterface $groupRepository)
    {
        parent::__construct($validator);
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
