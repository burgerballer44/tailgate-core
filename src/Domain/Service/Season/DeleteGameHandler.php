<?php

namespace Tailgate\Domain\Service\Season;

use Tailgate\Application\Command\Season\DeleteGameCommand;
use Tailgate\Application\Validator\ValidatorInterface;
use Tailgate\Domain\Model\Season\GameId;
use Tailgate\Domain\Model\Season\Season;
use Tailgate\Domain\Model\Season\SeasonId;
use Tailgate\Domain\Model\Season\SeasonRepositoryInterface;

class DeleteGameHandler
{
    private $seasonRepository;

    public function __construct(SeasonRepositoryInterface $seasonRepository)
    {
        $this->seasonRepository = $seasonRepository;
    }

    public function handle(DeleteGameCommand $command)
    {
        $seasonId = $command->getSeasonId();
        $gameId = $command->getGameId();

        $season = $this->seasonRepository->get(SeasonId::fromString($seasonId));

        $season->deleteGame(GameId::fromString($gameId));

        $this->seasonRepository->add($season);
    }
}
