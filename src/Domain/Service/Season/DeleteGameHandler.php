<?php

namespace Tailgate\Domain\Service\Season;

use Tailgate\Application\Command\Season\DeleteGameCommand;
use Tailgate\Application\Validator\ValidatorInterface;
use Tailgate\Domain\Model\Season\GameId;
use Tailgate\Domain\Model\Season\Season;
use Tailgate\Domain\Model\Season\SeasonId;
use Tailgate\Domain\Model\Season\SeasonRepositoryInterface;
use Tailgate\Domain\Service\AbstractService;

class DeleteGameHandler extends AbstractService
{
    private $seasonRepository;

    public function __construct(ValidatorInterface $validator, SeasonRepositoryInterface $seasonRepository)
    {
        parent::__construct($validator);
        $this->seasonRepository = $seasonRepository;
    }

    public function handle(DeleteGameCommand $command)
    {
        $this->validate($command);
        
        $seasonId = $command->getSeasonId();
        $gameId = $command->getGameId();

        $season = $this->seasonRepository->get(SeasonId::fromString($seasonId));

        $season->deleteGame(GameId::fromString($gameId));

        $this->seasonRepository->add($season);
    }
}
