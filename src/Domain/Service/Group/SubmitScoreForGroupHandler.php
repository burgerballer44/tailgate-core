<?php

namespace Tailgate\Domain\Service\Group;

use Tailgate\Application\Command\Group\SubmitScoreForGroupCommand;
use Tailgate\Application\Validator\ValidatorInterface;
use Tailgate\Domain\Model\Group\GroupId;
use Tailgate\Domain\Model\Group\GroupRepositoryInterface;
use Tailgate\Domain\Model\Group\PlayerId;
use Tailgate\Domain\Model\Season\GameId;
use Tailgate\Domain\Service\Validatable;
use Tailgate\Domain\Service\ValidatableService;

class SubmitScoreForGroupHandler implements ValidatableService
{
    use Validatable;
    
    private $validator;
    private $groupRepository;

    public function __construct(ValidatorInterface $validator, GroupRepositoryInterface $groupRepository)
    {
        $this->validator = $validator;
        $this->groupRepository = $groupRepository;
    }

    public function handle(SubmitScoreForGroupCommand $command)
    {
        $this->validate($command);

        $groupId = $command->getGroupId();
        $playerId = $command->getPlayerId();
        $gameId = $command->getGameId();
        $homeTeamPrediction = $command->getHomeTeamPrediction();
        $awayTeamPrediction = $command->getAwayTeamPrediction();

        $group = $this->groupRepository->get(GroupId::fromString($groupId));

        $group->submitScore(
            PlayerId::fromString($playerId),
            GameId::fromString($gameId),
            $homeTeamPrediction,
            $awayTeamPrediction
        );

        $this->groupRepository->add($group);
    }
}
