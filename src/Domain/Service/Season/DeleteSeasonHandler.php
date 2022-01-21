<?php

namespace Tailgate\Domain\Service\Season;

use Tailgate\Application\Command\Season\DeleteSeasonCommand;
use Tailgate\Application\Validator\ValidatorInterface;
use Tailgate\Domain\Model\Common\Date;
use Tailgate\Domain\Model\Season\Season;
use Tailgate\Domain\Model\Season\SeasonId;
use Tailgate\Domain\Model\Season\SeasonRepositoryInterface;
use Tailgate\Domain\Service\Clock\Clock;

class DeleteSeasonHandler
{
    private $seasonRepository;
    private $clock;

    public function __construct(SeasonRepositoryInterface $seasonRepository, Clock $clock)
    {
        $this->seasonRepository = $seasonRepository;
        $this->clock = $clock;
    }

    public function handle(DeleteSeasonCommand $command)
    {
        $season = $this->seasonRepository->get(SeasonId::fromString($command->getSeasonId()));

        $season->delete(Date::fromDateTimeImmutable($this->clock->currentTime()));

        $this->seasonRepository->add($season);
    }
}
