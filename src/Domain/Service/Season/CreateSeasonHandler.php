<?php

namespace Tailgate\Domain\Service\Season;

use Tailgate\Application\Command\Season\CreateSeasonCommand;
use Tailgate\Application\Validator\ValidatorInterface;
use Tailgate\Domain\Model\Season\Season;
use Tailgate\Domain\Model\Season\SeasonRepositoryInterface;
use Tailgate\Domain\Service\AbstractService;

class CreateSeasonHandler extends AbstractService
{
    public $seasonRepository;

    public function __construct(ValidatorInterface $validator, SeasonRepositoryInterface $seasonRepository)
    {
        parent::__construct($validator);
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
