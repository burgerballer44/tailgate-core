<?php

namespace Tailgate\Domain\Service\Season;

use Tailgate\Application\Command\Season\UpdateGameScoreCommand;
use Tailgate\Application\Validator\ValidatorInterface;
use Tailgate\Domain\Model\Season\GameId;
use Tailgate\Domain\Model\Season\SeasonId;
use Tailgate\Domain\Model\Season\SeasonRepositoryInterface;
use Tailgate\Domain\Service\Validatable;
use Tailgate\Domain\Service\ValidatableService;

class UpdateGameScoreHandler implements ValidatableService
{
    use Validatable;
    
    private $validator;
    private $seasonRepository;

    public function __construct(ValidatorInterface $validator, SeasonRepositoryInterface $seasonRepository)
    {
        $this->validator = $validator;
        $this->seasonRepository = $seasonRepository;
    }

    public function handle(UpdateGameScoreCommand $command)
    {
        $this->validate($command);
        
        $seasonId = $command->getSeasonId();
        $gameId = $command->getGameId();
        $homeTeamScore = $command->getHomeTeamScore();
        $awayTeamScore = $command->getAwayTeamScore();
        $startDate = $command->getStartDate();
        $startTime = $command->getStartTime();

        $season = $this->seasonRepository->get(SeasonId::fromString($seasonId));

        $season->updateGameScore(
            GameId::fromString($gameId),
            $homeTeamScore,
            $awayTeamScore,
            $startDate,
            $startTime
        );
        
        $this->seasonRepository->add($season);
    }
}
