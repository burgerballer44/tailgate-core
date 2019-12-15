<?php

namespace Tailgate\Application\Command\Season;

use Tailgate\Domain\Model\Season\SeasonRepositoryInterface;
use Tailgate\Domain\Model\Season\GameId;
use Tailgate\Domain\Model\Season\SeasonId;

class UpdateSeasonHandler
{
    public $seasonRepository;

    public function __construct(SeasonRepositoryInterface $seasonRepository)
    {
        $this->seasonRepository = $seasonRepository;
    }

    public function handle(UpdateSeasonCommand $command)
    {
        $seasonId = $command->getSeasonId();
        $sport = $command->getSport();
        $seasonType = $command->getSeasonType();
        $name = $command->getName();
        $seasonStart = $command->getSeasonStart();
        $seasonEnd = $command->getSeasonEnd();

        $season = $this->seasonRepository->get(SeasonId::fromString($seasonId));

        $season->update(
            $sport,
            $seasonType,
            $name,
            $seasonStart,
            $seasonEnd
        );
        
        $this->seasonRepository->add($season);
    }
}
