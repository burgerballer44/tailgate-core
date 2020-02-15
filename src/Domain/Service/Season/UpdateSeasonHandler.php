<?php

namespace Tailgate\Domain\Service\Season;

use Tailgate\Application\Command\Season\UpdateSeasonCommand;
use Tailgate\Application\Validator\ValidatorInterface;
use Tailgate\Domain\Model\Season\GameId;
use Tailgate\Domain\Model\Season\SeasonId;
use Tailgate\Domain\Model\Season\SeasonRepositoryInterface;
use Tailgate\Domain\Service\Validatable;
use Tailgate\Domain\Service\ValidatableService;

class UpdateSeasonHandler implements ValidatableService
{
    use Validatable;
    
    private $validator;
    private $seasonRepository;

    public function __construct(ValidatorInterface $validator, SeasonRepositoryInterface $seasonRepository)
    {
        $this->validator = $validator;
        $this->seasonRepository = $seasonRepository;
    }

    public function handle(UpdateSeasonCommand $command)
    {
        $this->validate($command);
        
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
