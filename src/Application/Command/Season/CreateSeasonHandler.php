<?php

namespace Tailgate\Application\Command\Season;

use Tailgate\Domain\Model\Season\Season;
use Tailgate\Domain\Model\Season\SeasonRepositoryInterface;

class CreateSeasonHandler
{
    public $seasonRepository;

    public function __construct(SeasonRepositoryInterface $seasonRepository)
    {
        $this->seasonRepository = $seasonRepository;
    }

    public function handle(CreateSeasonCommand $command)
    {
        $name = $command->getName();
        $sport = $command->getSport();
        $seasonType = $command->getSeasonType();
        $seasonStart = $command->getSeasonStart();
        $seasonEnd = $command->getSeasonEnd();

        $season = Season::create(
            $this->seasonRepository->nextIdentity(),
            $name,
            $sport,
            $seasonType,
            $seasonStart,
            $seasonEnd
        );
        
        $this->seasonRepository->add($season);
    }
}
