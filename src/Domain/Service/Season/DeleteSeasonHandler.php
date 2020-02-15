<?php

namespace Tailgate\Domain\Service\Season;

use Tailgate\Application\Command\Season\DeleteSeasonCommand;
use Tailgate\Application\Validator\ValidatorInterface;
use Tailgate\Domain\Model\Season\Season;
use Tailgate\Domain\Model\Season\SeasonId;
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
