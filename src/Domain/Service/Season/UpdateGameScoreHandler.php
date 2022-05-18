<?php

namespace Tailgate\Domain\Service\Season;

use Tailgate\Application\Command\Season\UpdateGameScoreCommand;
use Tailgate\Application\Validator\ValidatorInterface;
use Tailgate\Domain\Model\Common\Date;
use Tailgate\Domain\Model\Common\DateOrString;
use Tailgate\Domain\Model\Common\TimeOrString;
use Tailgate\Domain\Model\Season\GameId;
use Tailgate\Domain\Model\Season\SeasonId;
use Tailgate\Domain\Model\Season\SeasonRepositoryInterface;
use Tailgate\Domain\Service\Clock\Clock;
use Tailgate\Domain\Service\Validatable;
use Tailgate\Domain\Service\ValidatableService;

class UpdateGameScoreHandler implements ValidatableService
{
    use Validatable;

    private $validator;
    private $clock;
    private $seasonRepository;

    public function __construct(ValidatorInterface $validator, Clock $clock, SeasonRepositoryInterface $seasonRepository)
    {
        $this->validator = $validator;
        $this->clock = $clock;
        $this->seasonRepository = $seasonRepository;
    }

    public function handle(UpdateGameScoreCommand $command)
    {
        $this->validate($command);

        $season = $this->seasonRepository->get(SeasonId::fromString($command->getSeasonId()));

        $season->updateGameScore(
            GameId::fromString($command->getGameId()),
            $command->getHomeTeamScore(),
            $command->getAwayTeamScore(),
            DateOrString::fromString($command->getStartDate()),
            TimeOrString::fromString($command->getStartTime()),
            Date::fromDateTimeImmutable($this->clock->currentTime())
        );

        $this->seasonRepository->add($season);
    }
}
