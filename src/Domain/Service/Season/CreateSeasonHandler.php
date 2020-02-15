<?php

namespace Tailgate\Domain\Service\Season;

use Tailgate\Application\Command\Season\CreateSeasonCommand;
use Tailgate\Application\Validator\ValidatorInterface;
use Tailgate\Domain\Model\Season\Season;
use Tailgate\Domain\Model\Season\SeasonRepositoryInterface;
use Tailgate\Domain\Service\Validatable;
use Tailgate\Domain\Service\ValidatableService;

class CreateSeasonHandler implements ValidatableService
{
    use Validatable;
    
    private $validator;
    private $seasonRepository;

    public function __construct(ValidatorInterface $validator, SeasonRepositoryInterface $seasonRepository)
    {
        $this->validator = $validator;
        $this->seasonRepository = $seasonRepository;
    }

    public function handle(CreateSeasonCommand $command)
    {
        $this->validate($command);
        
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
