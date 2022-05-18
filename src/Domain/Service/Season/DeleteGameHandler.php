<?php

namespace Tailgate\Domain\Service\Season;

use Tailgate\Application\Command\Season\DeleteGameCommand;
use Tailgate\Domain\Model\Common\Date;
use Tailgate\Domain\Model\Season\GameId;
use Tailgate\Domain\Model\Season\SeasonId;
use Tailgate\Domain\Model\Season\SeasonRepositoryInterface;
use Tailgate\Domain\Service\Clock\Clock;

class DeleteGameHandler
{
    private $seasonRepository;
    private $clock;

    public function __construct(SeasonRepositoryInterface $seasonRepository, Clock $clock)
    {
        $this->seasonRepository = $seasonRepository;
        $this->clock = $clock;
    }

    public function handle(DeleteGameCommand $command)
    {
        $season = $this->seasonRepository->get(SeasonId::fromString($command->getSeasonId()));

        $season->deleteGame(GameId::fromString($command->getGameId()), Date::fromDateTimeImmutable($this->clock->currentTime()));

        $this->seasonRepository->add($season);
    }
}
