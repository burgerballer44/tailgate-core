<?php

namespace Tailgate\Application\Command\Season;

use Tailgate\Domain\Model\Season\SeasonRepositoryInterface;
use Tailgate\Domain\Model\Team\TeamId;
use Tailgate\Domain\Model\Season\SeasonId;

class AddGameHandler
{
    public $seasonRepository;

    public function __construct(SeasonRepositoryInterface $seasonRepository)
    {
        $this->seasonRepository = $seasonRepository;
    }

    public function handle(AddGameCommand $command)
    {
        $seasonId = $command->getSeasonId();
        $homeTeamId = $command->getHomeTeamId();
        $awayTeamId = $command->getAwayTeamId();
        $startDate = $command->getStartDate();

        $season = $this->seasonRepository->get(SeasonId::fromString($seasonId));

        $season->addGame(
            TeamId::fromString($homeTeamId),
            TeamId::fromString($awayTeamId),
            \DateTimeImmutable::createFromFormat('Y-m-d H:i', $startDate)
        );
        
        $this->seasonRepository->add($season);
    }
}
