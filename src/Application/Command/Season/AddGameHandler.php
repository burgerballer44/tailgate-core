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

    public function handle(AddGameCommand $addGameCommand)
    {
        $seasonId = $addGameCommand->getSeasonId();
        $homeTeamId = $addGameCommand->getHomeTeamId();
        $awayTeamId = $addGameCommand->getAwayTeamId();
        $startDate = $addGameCommand->getStartDate();

        $season = $this->seasonRepository->get(SeasonId::fromString($seasonId));

        $season->addGame(
            TeamId::fromString($homeTeamId),
            TeamId::fromString($awayTeamId),
            \DateTimeImmutable::createFromFormat('Y-m-d', $startDate)
        );
        
        $this->seasonRepository->add($season);
    }
}
