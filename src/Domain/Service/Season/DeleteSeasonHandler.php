<?php

namespace Tailgate\Domain\Service\Season;

use Tailgate\Application\Command\Season\DeleteSeasonCommand;
use Tailgate\Application\Validator\ValidatorInterface;
use Tailgate\Domain\Model\Season\Season;
use Tailgate\Domain\Model\Season\SeasonId;
use Tailgate\Domain\Model\Season\SeasonRepositoryInterface;
use Tailgate\Domain\Service\AbstractService;

class DeleteSeasonHandler extends AbstractService
{
    private $seasonRepository;

    public function __construct(ValidatorInterface $validator, SeasonRepositoryInterface $seasonRepository)
    {
        parent::__construct($validator);
        $this->seasonRepository = $seasonRepository;
    }

    public function handle(DeleteSeasonCommand $command)
    {
        $this->validate($command);
        
        $seasonId = $command->getSeasonId();

        $season = $this->seasonRepository->get(SeasonId::fromString($seasonId));

        $season->delete();

        $this->seasonRepository->add($season);
    }
}
