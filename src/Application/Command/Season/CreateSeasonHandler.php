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
        $sport = $command->getSport();
        $seasonType = $command->getSeasonType();
        $name = $command->getName();
        $seasonStart = $command->getSeasonStart();
        $seasonEnd = $command->getSeasonEnd();

        $season = Season::create(
            $this->seasonRepository->nextIdentity(),
            $sport,
            $seasonType,
            $name,
            \DateTimeImmutable::createFromFormat('Y-m-d', $seasonStart),
            \DateTimeImmutable::createFromFormat('Y-m-d', $seasonEnd)
        );
        
        $this->seasonRepository->add($season);
    }
}
