<?php

namespace Tailgate\Application\Command\Season;

use Tailgate\Domain\Model\Season\Season;
use Tailgate\Domain\Model\Season\SeasonId;
use Tailgate\Domain\Model\Season\GameId;
use Tailgate\Domain\Model\Season\SeasonRepositoryInterface;

class DeleteSeasonHandler
{
    private $seasonRepository;

    public function __construct(SeasonRepositoryInterface $seasonRepository)
    {
        $this->seasonRepository = $seasonRepository;
    }

    public function handle(DeleteSeasonCommand $command)
    {
        $seasonId = $command->getSeasonId();

        $season = $this->seasonRepository->get(SeasonId::fromString($seasonId));

        $season->delete();

        $this->seasonRepository->add($season);
    }
}
