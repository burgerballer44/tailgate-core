<?php

namespace Tailgate\Domain\Service\Season;

use Tailgate\Application\Command\Season\AddGameCommand;
use Tailgate\Application\Validator\ValidatorInterface;
use Tailgate\Domain\Model\Season\SeasonId;
use Tailgate\Domain\Model\Season\SeasonRepositoryInterface;
use Tailgate\Domain\Model\Team\TeamId;
use Tailgate\Domain\Service\AbstractService;

class AddGameHandler extends AbstractService
{
    public $seasonRepository;

    public function __construct(ValidatorInterface $validator, SeasonRepositoryInterface $seasonRepository)
    {
        parent::__construct($validator);
        $this->seasonRepository = $seasonRepository;
    }

    public function handle(AddGameCommand $command)
    {
        $this->validate($command);
        
        $seasonId = $command->getSeasonId();
        $homeTeamId = $command->getHomeTeamId();
        $awayTeamId = $command->getAwayTeamId();
        $startDate = $command->getStartDate();
        $startTime = $command->getStartTime();

        $season = $this->seasonRepository->get(SeasonId::fromString($seasonId));

        $season->addGame(
            TeamId::fromString($homeTeamId),
            TeamId::fromString($awayTeamId),
            $startDate,
            $startTime
        );
        
        $this->seasonRepository->add($season);
    }
}
